<template>
  <el-dialog
    v-el-drag-dialog
    class="uploader"
    :visible.sync="showModal"
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
      :field="field"
      :target="target"
      :headers="headers"
      :query="query"
      :fast-transfer="fastTransfer"
      :queue-limit="queueLimit"
      :size-limit="sizeLimit"
      :type-limit="typeLimit"
      :crop-open="cropOpen"
      :crop-width="cropWidth"
      :crop-height="cropHeight"
      :crop-fixed="cropFixed"
      :crop-output-quantity="cropOutputQuantity"
      :crop-output-type="cropOutputType"
      :empty-on-complete="true"
      :spark-unique="sparkUnique"
      :dialog-visible="showModal"
      @complete="complete"
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
      type: [String, Number],
      default: undefined
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
    query: {
      type: Function,
      default() {
        return function(file, chunk) {
          return {}
        }
      }
    },
    fastTransfer: {
      type: [String, Boolean],
      default: 'auto'
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
    sparkUnique: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      showModal: false,
      // 上传框总宽度
      width: '890px',
      // 显示的标题
      showTitle: ''
    }
  },
  watch: {
    value() {
      this.showModal = true
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.showTitle = this.title ? this.title : '文件上传'
    })
  },
  methods: {
    complete(message) {
      this.$emit('complete', message)
      this.showModal = false
    },
    handleClose() {
      this.showModal = false
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
