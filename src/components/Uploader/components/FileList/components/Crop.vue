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
      <div class="content" :style="{'height':height}">
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
    <canvas v-show="false" ref="canvas" :width="cropWidth" :height="cropHeight" />
    <div slot="footer" class="dialog-footer">
      <el-button size="medium" @click="handleClose">关闭</el-button>
      <el-button v-show="!error" type="primary" size="medium" :loading="loading" @click="finish">
        {{ loading? '裁剪中':'确认' }}
      </el-button>
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
    id: {
    // 传入图片的识别id
      type: Number,
      default: undefined
    },
    file: {
      // 传入的图片文件
      type: File,
      default: undefined
    },
    cropWidth: {
      // 初始截取框宽度
      type: Number,
      default: 0
    },
    cropHeight: {
      // 初始截取框高度
      type: Number,
      default: 0
    },
    cropFixed: {
      // 截图框宽高控制
      // true 固定大小，无法改变
      // [宽度, 高度] 只能按照比例改变（如[1, 1]）
      // false 不固定大小和比例，可自由改变
      type: [Boolean, Array],
      default: false
    },
    cropOutputQuantity: {
      // 输出图片质量（0.1 - 1）
      type: Number,
      default: 1
    },
    cropOutputType: {
      // 输出图片格式（支持jpg和png）
      type: String,
      default: 'png'
    }
  },
  data() {
    let fixed = true
    let fixedNumber = [1, 1]
    let fixedBox = false
    if (this.cropFixed === true) {
      fixed = false
      fixedBox = true
    } else if (this.cropFixed === false) {
      fixed = false
    } else {
      fixedNumber = this.cropFixed
    }
    return {
      // 裁剪组件的基础配置option
      error: false,
      image: undefined,
      option: {
        outputSize: this.cropOutputQuantity, // 裁剪生成图片的质量
        outputType: this.cropOutputType,
        autoCrop: true, // 是否默认生成截图框
        autoCropWidth: this.cropWidth, // 默认生成截图框宽度
        autoCropHeight: this.cropHeight, // 默认生成截图框高度
        fixed: fixed, // 是否开启截图框宽高固定比例
        fixedNumber: fixedNumber, // 截图框的宽高比例
        fixedBox: fixedBox,
        centerBox: true,
        high: false,
        original: true,
        infoTrue: true
      },
      loading: false
    }
  },
  computed: {
    height() {
      if (this.cropHeight <= 300) {
        return '300px'
      } else {
        return this.cropHeight + 'px'
      }
    }
  },
  watch: {
    value(value) {
      if (value) {
        this.init()
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
      if (this.loading) {
        return false
      }
      num = num || 1
      this.$refs.cropper.changeScale(num)
    },
    rotateLeft() {
      if (this.loading) {
        return false
      }
      this.$refs.cropper.rotateLeft()
    },
    rotateRight() {
      if (this.loading) {
        return false
      }
      this.$refs.cropper.rotateRight()
    },
    finish() {
      if (this.error) {
        this.$emit('close')
      } else {
        this.loading = true
        this.$refs.cropper.getCropBlob(data => {
          this.$emit('complete', this.id, this.file.name, data)
          this.loading = false
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
  margin-bottom: 20px;
}
.el-button-group {
  display: block;
}
</style>
