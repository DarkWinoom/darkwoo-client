<template>
  <el-dialog
    v-el-drag-dialog
    class="uploader"
    :visible.sync="value"
    :modal-append-to-body="false"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    :append-to-body="true"
    :title="showTitle"
    :before-close="handleClose"
    :width="width"
    top="5vh"
  >
    <uploader
      :dialog-visible="value"
      :field="field"
      :target="target"
      :headers="headers"
      :queue-limit="queueLimit"
      :size-limit="sizeLimit"
      :type-limit="typeLimit"
      :crop-open="cropOpen"
      :crop-width="cropWidth"
      :crop-height="cropHeight"
      :crop-fixed="cropFixed"
      :crop-output-quantity="cropOutputQuantity"
      :crop-output-type="cropOutputType"
      :lang="lang"
    />
    <div slot="footer" class="dialog-footer">
      <el-button size="medium" @click="handleClose">关闭</el-button>
    </div>
  </el-dialog>
</template>

<script>
import elDragDialog from '@/directive/el-drag-dialog'
import Uploader from './index'

export default {
  name: 'UploaderModal',
  directives: { elDragDialog },
  components: { Uploader },
  props: {
    value: {
      type: Boolean,
      default: false
    },
    title: {
      // dialog框显示的标题名称
      type: String,
      default: ''
    },
    field: {
      type: String,
      default: 'upload'
    },
    target: {
      type: String,
      default: ''
    },
    headers: {
      type: Object,
      default() {
        return {}
      }
    },
    queueLimit: {
      type: Number,
      default: 0
    },
    sizeLimit: {
      type: Number,
      default: 0
    },
    typeLimit: {
      type: Array,
      default() {
        return []
      }
    },
    cropOpen: {
      type: Boolean,
      default: true
    },
    cropWidth: {
      type: Number,
      default: 0
    },
    cropHeight: {
      type: Number,
      default: 0
    },
    cropFixed: {
      type: [Boolean, Array],
      default: false
    },
    cropOutputQuantity: {
      type: Number,
      default: 1
    },
    cropOutputType: {
      type: String,
      default: 'png'
    },
    lang: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      // 上传框总宽度
      width: '890px',
      // 显示的标题
      showTitle: ''
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.showTitle = this.title ? this.title : '文件上传'
    })
  },
  methods: {
    handleClose() {
      this.$emit('close')
    }
  }
}
</script>
<style lang="scss">
.uploader {
  .el-dialog__body {
    padding: 0 20px 20px;
  }
}
</style>
