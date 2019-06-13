<template>
  <el-dialog
    v-el-drag-dialog
    :visible.sync="value"
    :modal-append-to-body="false"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    :append-to-body="true"
    :title="dialogTitle"
    :before-close="handleClose"
    :width="width"
    top="5vh"
  >
    <el-upload
      class="upload-content"
      drag
      action="https://jsonplaceholder.typicode.com/posts/"
      multiple
    >
      <i class="el-icon-upload" />
      <div class="el-upload__text">
        将文件拖到此处，或
        <em>点击上传</em>
      </div>
      <div slot="tip" class="el-upload__tip">
        <p>格式限制：不限</p>
        <p>裁剪限制：120 x 120（必须是图片且尺寸大于限制）</p>
        <p>单文件大小限制：500 KB</p>
      </div>
    </el-upload>
    <div slot="footer" class="dialog-footer">
      <el-button type="primary" size="medium" icon="el-icon-upload" :loading="loading">开始上传</el-button>
      <el-button icon="el-icon-delete" size="medium">清空</el-button>
      <el-button size="medium" @click="handleClose">取消</el-button>
    </div>
  </el-dialog>
</template>
<script>
import elDragDialog from '@/directive/el-drag-dialog'
import { getToken } from '@/utils/auth'

export default {
  name: 'Uploader',
  directives: { elDragDialog },
  props: {
    value: {
      // 隐藏 / 显示控件
      type: Boolean,
      default: false
    },
    title: {
      // dialog框显示的标题名称
      type: String,
      default: ''
    },
    option: {
      type: Object,
      default() {
        return {
          // 目标上传 URL
          target: '',

          // 额外的请求头
          headers: {},

          // 队列中允许的最大文件数量（默认不限）
          queueSize: 0,

          // 单文件大小限制（单位kb，默认不限）
          sizeLimit: 0,

          // 上传文件格式限制，支持扩展名与image、video、audio（默认不限）
          // example：['zip','image','pdf','doc','docx']
          typeLimit: [],

          // 裁剪功能设定（仅支持图片）
          crop: {
            open: true, // 是否开启截取功能（自动识别图片）
            width: 120, // 初始截取框宽度
            height: 120, // 初始截取框高度
            fixed: [1, 1], // 固定宽高比，可设定为true（固定截取框）或者false（自由调整）
            quantity: 1, // 输出图片质量（0.1 - 1）
            avatarMod: false // 是否开启头像预览模式
          }
        }
      }
    },
    lang: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      loading: false,
      width: '',
      showTitle: ''
    }
  },
  computed: {
    getHeader() {
      return { 'Access-Token': getToken() }
    },
    dialogTitle() {
      return this.title ? this.title : '文件上传'
    },
    language() {
      let language
      if (this.lang) {
        language = this.lang
      } else {
        if (navigator.appName === 'Netscape') {
          language = navigator.language
        } else {
          language = navigator.browserLanguage
        }
      }
      return language.toLowerCase()
    }
  },
  created() {
    this.handleQueueSizeChange(0)
  },
  methods: {
    handleQueueSizeChange(size) {
      if (size > 0) {
        this.width = '905px'
      } else {
        this.width = '590px'
      }
    },
    handleClose() {
      this.$emit('close')
    }
  }
}
</script>
<style lang="scss">
.upload-content {
  .el-upload {
    display: block;
    .el-upload-dragger {
      width: 100%;
    }
  }
  .el-upload__tip {
    font-size: 14px;
    color: #909399;
    line-height: 28px;
    margin-top: 10px;
    p {
      margin: 0;
    }
  }
}
</style>

