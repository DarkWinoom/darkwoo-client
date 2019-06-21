<template>
  <div>
    <template v-if="support">
      <slot />
    </template>
    <template v-else>
      <div class="el-upload el-upload--text">
        <div class="el-upload-dragger">
          <i class="el-icon-warning" />
          <div class="el-upload__text">浏览器不支持该功能，请使用IE10以上或其他浏览器！</div>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
export default {
  name: 'UploaderSupportCheck',
  props: {
    support: {
      type: Boolean,
      default: true
    }
  }
}
</script>

<style lang="scss" scoped>
.el-icon-warning {
  font-size: 67px;
  color: #c0c4cc;
  margin: 40px 0 16px;
  line-height: 50px;
}
</style>
