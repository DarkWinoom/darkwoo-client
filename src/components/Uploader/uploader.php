<?php
// simple-uploader demo Link:https://github.com/simple-uploader/
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, Content-Type, Cookie, Access-Token");

/**
 * chunkNumber: 当前块的次序，第一个块是 1，注意不是从 0 开始的。
 * totalChunks: 文件被分成块的总数。
 * chunkSize: 分块大小，根据 totalSize 和这个值你就可以计算出总共的块数。注意最后一块的大小可能会比这个要大。
 * currentChunkSize: 当前块的大小，实际大小。
 * totalSize: 文件总大小。
 * identifier: 这个就是每个文件的唯一标示。
 * filename: 文件名。
 * relativePath: 文件夹上传的时候文件的相对路径属性。 */

class uploader
{

    private $temporaryFolder;
    private $maxFileSize = '';
    private $fileParameterName = 'upload';
    private $folder;
    private $url;

    function __construct($temporaryFolder)
    {
        $this->folder = $temporaryFolder;
        $this->temporaryFolder = dirname(__FILE__) . DIRECTORY_SEPARATOR . $temporaryFolder;
        $_root = rtrim(dirname(rtrim($_SERVER['SCRIPT_NAME'], '/')), '/');
        /*$domain = $_SERVER['HTTP_HOST'] . (($_root == '/' || $_root == '\\') ? '' : $_root);
        if (!empty($_SERVER['HTTPS'])) {
            $this->url = 'https://' . $domain;
        } else {
            $this->url = 'http://' . $domain;
        }*/
        $this->url = 'http://localhost' . (($_root == '/' || $_root == '\\') ? '' : $_root);
    }

    private function cleanIdentifier($identifier)
    {
        return preg_replace("/[^0-9A-Za-z_-]/U", '', $identifier);
    }

    private function getChunkFilename($chunkNumber, $identifier)
    {
        // Clean up the identifier
        $identifier = $this->cleanIdentifier($identifier);
        // What would the file name be?
        return $this->temporaryFolder . DIRECTORY_SEPARATOR . 'chunk' . DIRECTORY_SEPARATOR . 'uploader-' . $identifier . '.' . $chunkNumber;
    }

    private function validateRequest($chunkNumber, $chunkSize, $totalSize, $identifier, $filename, $fileSize = 0)
    {
        // Clean up the identifier
        $identifier = $this->cleanIdentifier($identifier);

        // Check if the request is sane
        if ($chunkNumber == 0 || $chunkSize == 0 || $totalSize == 0 || !$identifier || !$filename) {
            return 'non_uploader_request';
        }
        $numberOfChunks = max(floor($totalSize / ($chunkSize * 1.0)), 1);
        if ($chunkNumber > $numberOfChunks) {
            return 'invalid_uploader_request1';
        }

        // Is the file too big?
        if ($this->maxFileSize && $totalSize > $this->maxFileSize) {
            return 'invalid_uploader_request2';
        }

        if (!empty($fileSize)) {
            if ($chunkNumber < $numberOfChunks && $fileSize != $chunkSize) {
                // The chunk in the POST request isn't the correct size
                return 'invalid_uploader_request3';
            }
            if ($numberOfChunks > 1 && $chunkNumber == $numberOfChunks && $fileSize != (($totalSize % $chunkSize) + (int)$chunkSize)) {
                // The chunks in the POST is the last one, and the fil is not the correct size
                return 'invalid_uploader_request4';
            }
            if ($numberOfChunks == 1 && $fileSize != $totalSize) {
                // The file is only a single chunk, and the data size does not fit
                return 'invalid_uploader_request5';
            }
        }

        return 'valid';
    }

    public function file_exists()
    {
        // 通过 GET 请求判断文件是否存在
        $path_info = pathinfo($_GET['filename']);
        $extension = '.' . strtolower($path_info['extension']);
        $hash_name = $_GET['identifier'] . $extension;
        return file_exists($this->temporaryFolder . DIRECTORY_SEPARATOR . $hash_name);
    }

    public function uploaded_chunks($identifier,$totalChunks)
    {
        // 检测哪些块已经上传了
        // 该检测会在第一次get请求以及每一次chunk post中执行一次，可以考虑使用一些缓存机制来加快处理速度（如数据库、redis等）
        $uploaded = array();
        for ($currentTestChunk = 1; $currentTestChunk <= $totalChunks; $currentTestChunk++) {
            // Recursion
            if (file_exists($this->getChunkFilename($currentTestChunk, $identifier))) {
                $uploaded[] = $currentTestChunk;
            }
        }
        return $uploaded;
    }

    public function upload()
    {
        // 接收文件
        $fields = $_POST;
        $files = $_FILES;

        $chunkNumber = $fields['chunkNumber'];
        $totalChunks = $fields['totalChunks'];
        $chunkSize = $fields['chunkSize'];
        $totalSize = $fields['totalSize'];
        $identifier = $this->cleanIdentifier($fields['identifier']);
        $filename = $fields['filename'];

        if (!$files[$this->fileParameterName] || !$files[$this->fileParameterName]['size']) {
            return array(
                'code' => 10000,
                'data' => 'invalid_uploader_request'
            );
        }

        $validation = $this->validateRequest($chunkNumber, $chunkSize, $totalSize, $identifier, $filename, $files[$this->fileParameterName]['size']);
        if ($validation == 'valid') {
            $chunkFilename = $this->getChunkFilename($chunkNumber, $identifier);

            // Save the chunk (TODO: OVERWRITE)
            if (rename($files[$this->fileParameterName]['tmp_name'], $chunkFilename)) {
                // Do we have all the chunks?
                if (count($this->uploaded_chunks($identifier,$totalChunks)) == $totalChunks) {
                    return $this->merge();
                } else {
                    return array(
                        'code' => 20000,
                        'data' => 'partly_done'
                    );
                }
            } else {
                return array(
                    'code' => 10000,
                    'data' => 'save_file_fail'
                );
            }
        } else {
            return $validation;
        }
    }

    public function merge()
    {
        // 上传合并
        //$file_id = $_POST['id'];
        $totalChunks = $_POST['totalChunks'];
        $fileName = $_POST['filename'];
        $identifier = $_POST['identifier'];

        $path_info = pathinfo($fileName);
        $extension = '.' . strtolower($path_info['extension']);
        $hash_name = $identifier . $extension;
        if (!$out = @fopen($this->temporaryFolder . DIRECTORY_SEPARATOR . $hash_name, "wb")) {
            return array(
                'code' => 10000,
                'data' => 'steam_error'
            );
        }
        if (flock($out, LOCK_EX)) {
            for ($index = 1; $index <= $totalChunks; $index++) {
                $file_part = $this->getChunkFilename($index, $identifier);
                if (!$in = @fopen($file_part, "rb")) {
                    break;
                }
                while ($buff = fread($in, 2048)) {
                    fwrite($out, $buff);
                }
                @fclose($in);
                @unlink($file_part);
            }
            flock($out, LOCK_UN);
        }
        @fclose($out);
        return array(
            'code' => 20000,
            'data' => $this->get_file($identifier,$fileName)
        );
    }

    public function get_file($identifier,$name){
        // 获取文件信息（数据库中）
        // 实际使用时只需要通过$identifier返回文件即可

        // 有一种情况需要注意，就是用户修改过文件后缀再上传。这种情况下文件的md5是完全一样的但是后缀不一样，应该被视为两个文件（即使其中一个无法正常使用）
        // 可以考虑以下几种解决方法：
        // 1.不使用md5作为文件的identifier，默认的identifier会将文件名（包括后缀）作为seed
        // 2.在数据库中identifier不唯一，获取文件时同时使用identifier和文件后缀（推荐）
        // 3.存储的文件不使用后缀，在获取时通过统一的地址生成一个带后缀的文件
        $path_info = pathinfo($name);
        $extension = '.' . strtolower($path_info['extension']);
        $hash_name = $identifier . $extension;
        return array(
            'id' => rand(1, 99999), // 来自数据库，文件的id
            //'file_id' => $file_id,// $file_id 来自前端自定义的ID
            'link' => $this->url . '/' . $this->folder . '/' . $hash_name
        );
    }

}

$uploader = new uploader('upload');

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // 上传
    $return = $uploader->upload();
    if($return['code'] !== 20000){
        // 错误
        http_code(500);
    }else{
        // 正确
        http_code(200);
    }
    echo json_encode($return);
}elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
    // 这里除了判断块文件是否上传成功（断点续传功能）外，还需要做一个支持快传功能的处理
    // 即通过指纹进行数据库比对，若文件已经上传则直接返回无需再次上传
    // 这里为了演示方便，直接通过扫描文件的方式判断是否已经上传过
    if ($uploader->file_exists()) {
        http_code(202);
        $data = $uploader->get_file($_GET['identifier'], $_GET['filename']);
    } else {
        http_code(201);
        $data = array(
            'id' => 0,
            'link' => '',
            'skipChunks' => $uploader->uploaded_chunks($_GET['identifier'], $_GET['totalChunks'])
        );
        // 为了保证因为意外终止导致数据不完整，因此在断点续传时选择将最后三个chunks舍弃（重新上传）
        // 这里的选择是源于simple-uploader配置中的simultaneousUploads，可以根据实际需要进行修改
        if (count($data['skipChunks']) <= 3) {
            $data['skipChunks'] = [];
        } else {
            array_splice($data['skipChunks'], -3);
        }
    }
    echo json_encode(array(
        'code' => 20000,
        'data' => $data,
    ));
}

function http_code($num)
{
    // 200, 201, 202: 当前块上传成功，不需要重传。
    // 404, 415. 500, 501: 当前块上传失败，会取消整个文件上传。
    $http = array(
        200 => "HTTP/1.1 200 OK",
        201 => "HTTP/1.1 201 Created",
        202 => "HTTP/1.1 202 Accepted",
        204 => "HTTP/1.1 204 No Content",
        404 => "HTTP/1.1 404 Not Found",
        415 => "HTTP/1.1 415 Unsupported Media Type",
        500 => "HTTP/1.1 500 Internal Server Error",
        501 => "HTTP/1.1 501 Not Implemented",
    );

    header($http[$num]);
}