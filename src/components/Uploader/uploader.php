<?php
/**
 * vue-element-uploader 示例（源生PHP）
 * 该示例仅为流程示例，可作为参考，切忌在实际项目中直接使用
 *
 * @auth DarkWinoom <archzars@vip.qq.com>
 */
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, Content-Type, Cookie, X-Token, X-Version");

class uploader
{

    private $temporaryFolder;
    private $maxFileSize = '';
    private $fileParameterName;
    private $uploadUrl;

    function __construct($fileParameterName, $temporaryFolder)
    {
        $this->fileParameterName = $fileParameterName;
        $this->temporaryFolder = dirname(__FILE__) . DIRECTORY_SEPARATOR . $temporaryFolder;
        $_root = rtrim(dirname(rtrim($_SERVER['SCRIPT_NAME'], '/')), '/');
        $domain = $_SERVER['HTTP_HOST'] . (($_root == '/' || $_root == '\\') ? '' : $_root);
        if (!empty($_SERVER['HTTPS'])) {
            $this->uploadUrl = 'https://' . $domain;
        } else {
            $this->uploadUrl = 'http://' . $domain;
        }
        // compose the upload http url
        $this->uploadUrl .= '/' . $temporaryFolder . '/';
    }

    private function cleanIdentifier($identifier)
    {
        return preg_replace("/[^0-9A-Za-z_-]/U", '', $identifier);
    }

    private function getChunkFile($chunkNumber, $identifier)
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
            return 'invalid_uploader_request: out of chunks';
        }

        // Is the file too big?
        if ($this->maxFileSize && $totalSize > $this->maxFileSize) {
            return 'invalid_uploader_request: size too big';
        }

        if (!empty($fileSize)) {
            if ($chunkNumber < $numberOfChunks && $fileSize != $chunkSize) {
                // The chunk in the POST request isn't the correct size
                return 'invalid_uploader_request: size not correct';
            }
            if ($numberOfChunks > 1 && $chunkNumber == $numberOfChunks && $fileSize != (($totalSize % $chunkSize) + (int)$chunkSize)) {
                // The chunks in the POST is the last one, and the fil is not the correct size
                return 'invalid_uploader_request: chunks number error';
            }
            if ($numberOfChunks == 1 && $fileSize != $totalSize) {
                // The file is only a single chunk, and the data size does not fit
                return 'invalid_uploader_request: chunk size error';
            }
        }

        return 'valid';
    }

    public function file_exists($identifier,$filename)
    {
        // 判断文件是否存在
        $path_info = pathinfo($filename);
        $extension = '.' . strtolower($path_info['extension']);
        $hash_name = $identifier . $extension;
        return file_exists($this->temporaryFolder . DIRECTORY_SEPARATOR . $hash_name);
    }

    public function uploaded_chunks($identifier,$totalChunks)
    {
        // 检测哪些块已经上传了
        // 该检测会在第一次get请求以及每一次chunk post中执行一次，可以考虑使用一些缓存机制来加快处理速度（如数据库、redis等）
        $uploaded = array();
        for ($currentTestChunk = 1; $currentTestChunk <= $totalChunks; $currentTestChunk++) {
            // Recursion
            if (file_exists($this->getChunkFile($currentTestChunk, $identifier))) {
                $uploaded[] = $currentTestChunk;
            }
        }
        return $uploaded;
    }

    public function upload()
    {
        // 接收文件
        $chunkNumber = post('chunkNumber');
        $totalChunks = post('totalChunks');
        $chunkSize = post('chunkSize');
        $totalSize = post('totalSize');
        $identifier = $this->cleanIdentifier(post('identifier'));
        $filename = post('filename');

        if (!$_FILES[$this->fileParameterName] || !$_FILES[$this->fileParameterName]['size']) {
            return array(
                'code' => 10000,
                'data' => 'invalid_uploader_request: file not found'
            );
        }

        $validation = $this->validateRequest($chunkNumber, $chunkSize, $totalSize, $identifier, $filename, $_FILES[$this->fileParameterName]['size']);
        if ($validation == 'valid') {
            $chunkFile = $this->getChunkFile($chunkNumber, $identifier);

            // Save the chunk (TODO: OVERWRITE)
            if (rename($_FILES[$this->fileParameterName]['tmp_name'], $chunkFile)) {
                // Do we have all the chunks?
                if ($totalChunks == 1) {
                    // if there only one chunk, directly merge
                    return $this->merge();
                } else {
                    return array(
                        'code' => 20000,
                        'data' => 'chunk '. $chunkNumber .' upload success'
                    );
                }
            } else {
                return array(
                    'code' => 10000,
                    'data' => 'save_file_fail: the directory could not be write'
                );
            }
        } else {
            return $validation;
        }
    }

    public function merge()
    {
        // 上传合并
        $totalChunks = post('totalChunks');
        $fileName = post('filename');
        $identifier = post('identifier');

        $path_info = pathinfo($fileName);
        $extension = '.' . strtolower($path_info['extension']);
        $hash_name = $identifier . $extension;
        if (!$out = @fopen($this->temporaryFolder . DIRECTORY_SEPARATOR . $hash_name, "wb")) {
            return array(
                'code' => 10000,
                'data' => 'open_file_fail: the chunk file could not be open'
            );
        }
        if (flock($out, LOCK_EX)) {
            for ($index = 1; $index <= $totalChunks; $index++) {
                $file_part = $this->getChunkFile($index, $identifier);
                if (file_exists($file_part)) {
                    $in = fopen($file_part, 'rb');
                    $content = fread($in, filesize($file_part));
                    fwrite($out, $content);
                    // if chunk size is too big, use the following instead:
                    /*while ($buff = fread($in, 2048)) {
                        fwrite($out, $buff);
                    }*/
                    fclose($in);
                } else {
                    return array(
                        'code' => 10000,
                        'data' => 'open_file_fail: the chunk '.$index.' is not exists'
                    );
                }
            }
            flock($out, LOCK_UN);
        }
        @fclose($out);
        for ($index = 1; $index <= $totalChunks; $index++) {
            $file_part = $this->getChunkFile($index, $identifier);
            if (file_exists($file_part)) {
                unlink($file_part);
            }
        }
        return array(
            'code' => 20000,
            'data' => $this->get_file($identifier,$fileName)
        );
    }

    public function get_file($identifier,$name){
        // 获取文件信息（数据库中）
        // 通过$identifier与数据库中存储的文件进行比对并返回（这里为演示）

        // 有一种情况需要注意，就是相同文件但后缀不同。这种情况下文件计算的spark-md5是一致的，但是后缀不一样，应该被视为两个文件（即使其中一个无法正常使用）
        // 可以考虑以下几种解决方法：
        // 1.不使用spark-md5作为文件的identifier，默认的identifier会将文件名（包括后缀）作为seed
        // 2.在数据库中identifier不唯一，获取文件时同时使用identifier和文件后缀（推荐）
        // 3.存储的文件不使用后缀，在获取时通过统一的地址生成一个带后缀的文件
        $path_info = pathinfo($name);
        $extension = '.' . strtolower($path_info['extension']);
        $hash_name = $identifier . $extension;
        return array(
            'id' => rand(1, 99999), // 来自数据库，文件的id（用来确定文件是否上传成功）
            'identifier' => $identifier,
            'name' => $name, // 原始文件名
            'link' => $this->uploadUrl . $hash_name // 文件的http访问地址
        );
    }

}

$uploader = new uploader('upload','upload');

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // 上传
    if(post('merge')){
        $return = $uploader->merge();
    } else {
        $return = $uploader->upload();
    }
    if($return['code'] !== 20000){
        // 错误
        http_code(500);
    }else{
        // 正确
        http_code(200);
    }
    echo json_encode($return);
}elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($uploader->file_exists($_GET['identifier'],$_GET['filename'])) {
        // 文件已存在，直接返回文件信息（快传）
        http_code(202);
        $data = $uploader->get_file($_GET['identifier'], $_GET['filename']);
    } else {
        // 文件不存在，正常上传
        http_code(201);
        $data = array(
            'id' => 0,
            'identifier' => $_GET['identifier'],
            'name' => $_GET['filename'],
            'link' => '',
            'skipChunks' => $uploader->uploaded_chunks($_GET['identifier'], $_GET['totalChunks']) // 已上传的块（断点续传）
        );
        /*if (count($data['skipChunks']) <= 3) {
            $data['skipChunks'] = [];
        } else {
            array_splice($data['skipChunks'], -3);
        }*/
        // 为了保证因为意外终止导致数据不完整，因此在断点续传时选择将最后三个chunks舍弃（通知前端重新上传）
        // 这里的选择是源于simple-uploader配置中的simultaneousUploads，可以根据实际需要进行修改
    }
    echo json_encode(array(
        'code' => 20000,
        'data' => $data,
    ));
}

function post($var,$default = '')
{
    // 获取 post 参数
    if (!empty($_POST[$var])) {
        return $_POST[$var];
    } else {
        $steam = json_decode(file_get_contents("php://input"), TRUE);
        return !empty($steam[$var]) ? $steam[$var] : $default;
    }
}

function http_code($num)
{
    // 200, 201, 202: 当前块上传成功，不需要重传。
    // 404, 415. 500, 501: 当前块上传失败，会取消整个文件上传。
    $http = array(
        200 => "HTTP/1.1 200 OK",
        201 => "HTTP/1.1 201 Created",
        202 => "HTTP/1.1 202 Accepted",
        404 => "HTTP/1.1 404 Not Found",
        415 => "HTTP/1.1 415 Unsupported Media Type",
        500 => "HTTP/1.1 500 Internal Server Error",
        501 => "HTTP/1.1 501 Not Implemented",
    );

    header($http[$num]);
}