<template>
  <el-dialog
    v-el-drag-dialog
    :visible.sync="value"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    :append-to-body="true"
    :before-close="handleClose"
    title="图片裁剪"
  >
    <div class="cropper-content">
      <div class="cropper" style="text-align:center">
        <vue-cropper
          ref="cropper"
          v-loading="loading"
          :img="option.img"
          :output-size="option.size"
          :auto-crop="option.autoCrop"
          :auto-crop-width="option.autoCropWidth"
          :auto-crop-height="option.autoCropHeight"
          :fixed="option.fixed"
          :fixed-number="option.fixedNumber"
          :full="option.full"
          :fixed-box="option.fixedBox"
          :center-box="option.centerBox"
        />
      </div>
    </div>
    <div slot="footer" class="dialog-footer">
      <el-button @click="handleClose">取 消</el-button>
      <el-button type="primary" :loading="loading" @click="finish">确认</el-button>
    </div>
  </el-dialog>
</template>

<script>
import elDragDialog from '@/directive/el-drag-dialog'
import { VueCropper } from 'vue-cropper'

export default {
  directives: { elDragDialog },
  components: { VueCropper },
  props: {
    value: {
      // 隐藏 / 显示控件
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      // 裁剪组件的基础配置option
      option: {
        img:
          'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif', // 裁剪图片的地址
        outputSize: 1, // 裁剪生成图片的质量
        autoCrop: true, // 是否默认生成截图框
        autoCropWidth: 200, // 默认生成截图框宽度
        autoCropHeight: 200, // 默认生成截图框高度
        fixed: false, // 是否开启截图框宽高固定比例
        fixedNumber: [1, 1], // 截图框的宽高比例
        full: true, // 是否输出原图比例的截图
        fixedBox: false,
        centerBox: true
      },
      loading: false
    }
  },
  methods: {
    // 点击裁剪，这一步是可以拿到处理后的地址
    finish() {
      this.$refs.cropper.getCropBlob(data => {
        this.loading = true
        // 上传
        console.log(data)
        setTimeout(() => {
          this.loading = false
        }, 500)
      })
    },
    handleClose() {
      this.$emit('close')
    }
  }
}
</script>

<style lang="scss" scoped>
.cropper-content {
  .cropper {
    width: auto;
    height: 300px;
  }
}
</style>
