
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
