
/**
 * 通过base64编码获取文件的大小
 *
 * @param {*} base64url 文件的base64编码
 * @param {*} mime 文件的mime类型
 * @return {[Integer]}
 */
export function getFileSizeByBase64(base64url, mime) {
  // 获取base64编码文件的大小
  let str = base64url.replace('data:' + mime + ';base64,', '')
  const equalIndex = str.indexOf('=')
  if (str.indexOf('=') > 0) {
    str = str.substring(0, equalIndex)
  }
  return parseInt(str.length - (str.length / 8) * 2)
}

/**
 * 格式化文件大小
 *
 * @param {[Integer]} filesize 文件大小
 * @return {[String]}
 */
export function formatSize(filesize) {
  if (filesize == null || !filesize) {
    return '0 B'
  }
  const unitArr = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
  let index = 0
  const srcsize = parseFloat(filesize)
  index = Math.floor(Math.log(srcsize) / Math.log(1024))
  let size = srcsize / Math.pow(1024, index)
  if (filesize < 1024 * 1024) {
    size = size.toFixed(0)
  } else {
    size = size.toFixed(2)
  }
  return size + ' ' + unitArr[index]
}

/**
 * database64文件格式转换为2进制
 *
 * @param  {[String]} data dataURL 的格式为 “data:image/png;base64,****”,逗号之前都是一些说明性的文字，我们只需要逗号之后的就行了
 * @param  {[String]} mime 输出mime类型
 * @return {[Blob]}
 */
export function data2blob(data, mime = 'image/png') {
  data = data.split(',')[1]
  data = window.atob(data)
  var ia = new Uint8Array(data.length)
  for (var i = 0; i < data.length; i++) {
    ia[i] = data.charCodeAt(i)
  }
  // canvas.toDataURL 返回的默认格式就是 image/png
  return new Blob([ia], {
    type: mime
  })
}

/**
 * 格式化时间（时分秒）
 *
 * @param {Integer} s 时间戳
 */
export function secondsToStr(s) {
  let t
  if (s > -1) {
    const hour = Math.floor(s / 3600)
    const min = Math.floor(s / 60) % 60
    const sec = s % 60
    if (hour < 10) {
      t = '0' + hour + ':'
    } else {
      t = hour + ':'
    }

    if (min < 10) { t += '0' }
    t += min + ':'
    if (sec < 10) { t += '0' }
    t += sec.toFixed(0)
  }
  return t
}
