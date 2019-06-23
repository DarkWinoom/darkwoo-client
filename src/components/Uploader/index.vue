<template>
  <el-dialog
    v-el-drag-dialog
    class="uploader"
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
    <support-check :support="support">
      <el-row :gutter="20">
        <el-col :span="8">
          <div ref="drop">
            <drop-field
              :queue-limit="queueLimit"
              :size-limit="sizeLimit"
              :type-limit="typeLimit"
              :crop-show="cropOpen"
              :crop-width="cropWidth"
              :crop-height="cropHeight"
            >
              将文件拖到此处，或
              <em>点击上传</em>
            </drop-field>
          </div>
        </el-col>
        <el-col :span="16">
          <file-list
            :frame-show="value"
            :files="files"
            :queue-limit="queueLimit"
            :crop-open="cropOpen"
            :crop-width="cropWidth"
            :crop-height="cropHeight"
            :crop-fixed="cropFixed"
            :crop-output-quantity="cropOutputQuantity"
            :crop-output-type="cropOutputType"
            @add-file="handleAddFile"
            @remove="handleRemove"
          />
        </el-col>
      </el-row>
    </support-check>
    <div slot="footer" class="dialog-footer">
      <el-button size="medium" @click="handleClose">关闭</el-button>
    </div>
  </el-dialog>
</template>
<script>
import MimeTypes from 'mime-types'
import Uploader from 'simple-uploader.js'
import elDragDialog from '@/directive/el-drag-dialog'
import { getToken } from '@/utils/auth'
import SupportCheck from './components/SupportCheck'
import DropField from './components/DropField'
import FileList from './components/FileList'

export default {
  name: 'Uploader',
  version: '0.2.13',
  provide() {
    return {
      uploader: this
    }
  },
  directives: { elDragDialog },
  components: {
    SupportCheck,
    DropField,
    FileList
  },
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
    field: {
      // 上传文件的域
      type: String,
      default: 'upload'
    },
    target: {
      // 上传目标url
      type: String,
      default: ''
    },
    headers: {
      // 额外的请求头
      type: Object,
      default() {
        return {}
      }
    },
    queueLimit: {
      // 队列中允许的最大文件数量（默认不限）
      type: Number,
      default: 0
    },
    sizeLimit: {
      // 单文件大小限制（默认不限）
      type: Number,
      default: 0
    },
    typeLimit: {
      // 上传文件格式限制，支持扩展名与image、video、audio（默认不限）
      type: Array,
      default() {
        return []
      }
    },
    cropOpen: {
      // 是否开启截取功能（自动识别图片）
      type: Boolean,
      default: true
    },
    cropWidth: {
      // 初始截取框宽度（0为默认值，容器的80%）
      type: Number,
      default: 0
    },
    cropHeight: {
      // 初始截取框高度（0为默认值，容器的80%）
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
    },
    lang: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      loading: false,
      // 上传框总宽度
      width: '890px',
      // 显示的标题
      showTitle: '',
      // 是否支持
      support: true,
      // simple-uploader 配置
      simpleUploaderOptions: {
        target: this.target,
        testChunks: false,
        fileParameterName: this.field,
        successStatuses: [200, 201, 202],
        permanentErrors: [206, 404, 415, 500, 501],
        singleFile: this.queueLimit === 1,
        initialPaused: true,
        headers: (file, chunk) => {
          return this.headers ? this.headers : {}
        },
        query: function(file, chunk) {
          return { id: file.id }
        }
      },
      files: []
    }
  },
  computed: {
    getHeader() {
      return { 'X-Token': getToken() }
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
    this.uploader = new Uploader(this.simpleUploaderOptions)
    this.support = this.uploader.support
    if (this.support) {
      this.uploader.on('fileAdded', this.fileAdded)
      this.uploader.on('fileRemoved', this.fileRemoved)
      this.uploader.on('filesSubmitted', this.filesSubmitted)
      this.uploader.on('fileError', this.fileError)
    }
  },
  mounted() {
    this.$nextTick(() => {
      if (this.support) {
        var accept = []
        if (this.typeLimit) {
          for (const type of this.typeLimit) {
            switch (type) {
              case 'image':
                accept.push('image/*')
                break
              case 'video':
                accept.push('video/*')
                break
              case 'audio':
                accept.push('audio/*')
                break
              default:
                if (MimeTypes.lookup(type)) {
                  accept.push(MimeTypes.lookup(type))
                }
            }
          }
        }
        this.uploader.assignBrowse(this.$refs.drop, false, false, {
          accept: accept.join(',')
        })
        if (this.$refs.drop) {
          this.uploader.assignDrop(this.$refs.drop)
        }
      }
    })
  },
  destroyed() {
    if (this.support) {
      this.uploader.off('fileAdded', this.fileAdded)
      this.uploader.off('fileRemoved', this.fileRemoved)
      this.uploader.off('filesSubmitted', this.filesSubmitted)
      this.uploader.off('fileError', this.fileError)
      // this.uploader.off('complete', this.complete)
      if (this.$refs.drop) {
        this.uploader.unAssignDrop(this.$refs.drop)
      }
    }
    console.log('Uploader has been destroyed')
  },
  methods: {
    ignoreNotify(file, title, type = 'warning') {
      // 自定义通知拼接
      setTimeout(() => {
        this.$notify({
          title: title,
          message: '文件"' + file.name + '"的添加已被忽略',
          type: type,
          duration: 6000
        })
      }, 100)
    },
    fileAdded(file, event) {
      // 格式与大小检测
      if (this.queueLimit > 0 && this.files.length >= this.queueLimit) {
        this.ignoreNotify(file, '文件数量超过限制')
        return false
      } else if (file.name.lastIndexOf('.') === -1) {
        this.ignoreNotify(file, '文件格式有误')
        return false
      } else if (this.typeLimit.length > 0) {
        const suffix = file.name
          .substring(file.name.lastIndexOf('.') + 1, file.name.length)
          .toLowerCase()
        let access = false
        for (const type of this.typeLimit) {
          switch (type) {
            case 'image':
            case 'video':
            case 'audio':
              if (file.fileType.indexOf(type) !== -1) {
                access = true
              }
              break
            default:
              if (type === suffix) {
                access = true
              }
          }
        }
        if (!access) {
          this.ignoreNotify(file, '文件格式不符合要求')
          return false
        }
      }
      if (this.sizeLimit && file.size > this.sizeLimit) {
        this.ignoreNotify(file, '文件大小超过限制')
        return false
      }
    },
    fileRemoved(file) {
      const index = this.files.findIndex(item => item.id === file.id)
      this.files.splice(index, 1)
    },
    filesSubmitted(files, fileList, event) {
      if (
        this.queueLimit > 0 &&
        this.files.length + files.length > this.queueLimit
      ) {
        const less = this.queueLimit - this.files.length
        for (const index in files) {
          this.files.push(files[index])
          if (index > less - 1) {
            this.ignoreNotify(files[index], '文件数量超过限制')
            this.remove(files[index])
          }
        }
      } else {
        this.files = [...this.files, ...files]
      }
    },
    fileError(rootFile, file, message, chunk) {
      console.log(file, message)
    },
    handleAddFile(file) {
      this.uploader.addFile(file)
    },
    handleRemove(file) {
      this.uploader.removeFile(file)
    },
    handleClose() {
      if (this.files.length > 0) {
        let uploading = false
        for (const file of this.files) {
          if (file.isUploading()) {
            uploading = true
          }
        }
        if (uploading) {
          this.$confirm('有文件正在上传中，是否关闭窗口?', '系统提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning'
          }).then(() => {
            for (const file of this.files) {
              file.pause()
            }
            this.$emit('close')
          })
        } else {
          this.$emit('close')
        }
      } else {
        this.$emit('close')
      }
    }
  }
}
</script>
<style lang="scss">
.uploader {
  .el-dialog__body {
    padding: 0 20px 20px;
    .upload-pane {
      .el-tabs__header {
        margin: 0;
      }
    }
  }
}
</style>

