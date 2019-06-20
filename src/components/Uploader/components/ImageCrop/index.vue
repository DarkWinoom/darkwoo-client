<template>
  <el-dialog
    v-el-drag-dialog
    class="cropper"
    :visible.sync="value"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    :append-to-body="true"
    :before-close="handleClose"
    title="图片裁剪"
  >
    <template v-if="!error">
      <div class="content">
        <vue-cropper
          ref="cropper"
          v-loading="loading"
          :img="image"
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
      <el-button-group>
        <el-button
          type="default"
          title="放大"
          size="medium"
          icon="el-icon-plus"
          plain
          @click="changeScale(1)"
        />
        <el-button
          type="default"
          title="缩小"
          size="medium"
          icon="el-icon-minus"
          plain
          @click="changeScale(-1)"
        />
        <el-button
          type="default"
          title="向左旋转"
          size="medium"
          icon="el-icon-refresh-left"
          plain
          @click="rotateLeft"
        />
        <el-button
          type="default"
          title="向右旋转"
          size="medium"
          icon="el-icon-refresh-right"
          plain
          @click="rotateRight"
        />
      </el-button-group>
    </template>
    <template v-else>
      <p>读取图片失败</p>
      <p>文件可能已经损坏</p>
    </template>
    <div slot="footer" class="dialog-footer">
      <el-button v-show="!error" size="medium" @click="handleClose">取 消</el-button>
      <el-button type="primary" size="medium" :loading="loading" @click="finish">确认</el-button>
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
    },
    file: {
      // 传入的图片文件
      type: File,
      default: undefined
    }
  },
  data() {
    return {
      // 裁剪组件的基础配置option
      error: false,
      image: undefined,
      option: {
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
  watch: {
    value(value) {
      this.init()
      if (value) {
        const fr = new FileReader()
        fr.onload = e => {
          const img = new Image()
          img.src = fr.result
          img.onerror = e => {
            this.error = true
          }
          img.onload = () => {
            this.image = img.src
          }
        }
        fr.onerror = e => {
          this.error = true
        }
        fr.readAsDataURL(this.file)
      }
    }
  },
  methods: {
    init() {
      this.image =
        'data:image/png;base64,UmFyIRoHAQAzkrXlCgEFBgAFAQGAgABRBebzLAIDC4gABIgAIPex5LiAAAAQ5rWL6K+V55SodHh0LnR4dAoDApWmBesFmNQBdGVzdLLiytQdd1ZRAwUEAA=='
      this.loading = false
      this.error = false
    },
    changeScale(num) {
      num = num || 1
      this.$refs.cropper.changeScale(num)
    },
    rotateLeft() {
      this.$refs.cropper.rotateLeft()
    },
    rotateRight() {
      this.$refs.cropper.rotateRight()
    },
    finish() {
      if (this.error) {
        this.$emit('close')
      } else {
        this.$refs.cropper.getCropBlob(data => {
          console.log(data)
          this.loading = true
          setTimeout(() => {
            this.loading = false
          }, 500)
        })
      }
    },
    handleClose() {
      this.$emit('close')
    }
  }
}
</script>

<style lang="scss">
.cropper {
  .el-dialog__body {
    padding: 10px 20px 0;
  }
}
</style>
<style lang="scss" scoped>
.content {
  width: auto;
  height: 300px;
  padding-bottom: 20px;
}
.el-button-group {
  display: block;
}
</style>
