<template>
  <div>
    <el-button type="success" size="small" @click="wechatHandleClick('wechat')">
      <svg-icon
        icon-class="wechat"
        class="icon"
      />
      微信登录
    </el-button>
    <el-button type="danger" size="small" @click="qqHandleClick('tencent')">
      <svg-icon
        icon-class="qq"
        class="icon"
      />
      QQ登录
    </el-button>
  </div>
</template>

<script>
// import openWindow from '@/utils/openWindow'

export default {
  name: 'SocialSignin',
  methods: {
    wechatHandleClick(thirdpart) {
      this.$message.success('wechat ok')
      // this.$store.commit('SET_AUTH_TYPE', thirdpart)
      // const appid = 'xxxxx'
      // const redirect_uri = encodeURIComponent('xxx/redirect?redirect=' + window.location.origin + '/auth-redirect')
      // const url = 'https://open.weixin.qq.com/connect/qrconnect?appid=' + appid + '&redirect_uri=' + redirect_uri + '&response_type=code&scope=snsapi_login#wechat_redirect'
      // openWindow(url, thirdpart, 540, 540)
    },
    qqHandleClick(thirdpart) {
      this.$message.success('qq ok')
      // this.$store.commit('SET_AUTH_TYPE', thirdpart)
      // const client_id = 'xxxxx'
      // const redirect_uri = encodeURIComponent('xxx/redirect?redirect=' + window.location.origin + '/auth-redirect')
      // const url = 'https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=' + client_id + '&redirect_uri=' + redirect_uri
      // openWindow(url, thirdpart, 540, 540)
    }
  }
}
</script>
