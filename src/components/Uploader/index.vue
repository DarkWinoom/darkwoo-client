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
    <template v-if="support">
      <el-row :gutter="20">
        <el-col :span="8">
          <div class="el-upload el-upload--text">
            <div ref="drop" class="el-upload-dragger">
              <i class="el-icon-upload" />
              <div class="el-upload__text">
                将文件拖到此处，或
                <em>点击上传</em>
              </div>
            </div>
          </div>
          <div class="el-upload__tip">
            <template v-if="queueLimit > 0">
              <p v-if="queueLimit === 1">请选择单一文件</p>
              <p v-else>数量限制：{{ queueLimit }}</p>
            </template>
            <p>格式限制：{{ typeLimitTips }}</p>
            <p v-show="cropOpen && cropFixed">尺寸建议：{{ cropWidth }} x {{ cropHeight }}</p>
            <p>单文件大小限制：{{ sizeLimitTips }}</p>
          </div>
        </el-col>
        <el-col :span="16">
          <file-list
            :frame-show="value"
            :files="files"
            :queue-limit="queueLimit"
            :crop-open="cropOpen"
            @remove="remove"
          />
          <image-crop v-model="showCrop" @close="showCrop = false" />
        </el-col>
      </el-row>
      <div slot="footer" class="dialog-footer">
        <el-button size="medium" @click="handleClose">关闭</el-button>
      </div>
    </template>
    <template v-else>
      <div class="el-upload el-upload--text">
        <div class="el-upload-dragger">
          <i class="el-icon-warning" />
          <div class="el-upload__text">浏览器不支持该功能，请使用IE10以上或其他浏览器！</div>
        </div>
      </div>
      <div slot="footer" class="dialog-footer">
        <el-button size="medium" @click="handleClose">关闭</el-button>
      </div>
    </template>
  </el-dialog>
</template>
<script>
import MimeTypes from 'mime-types'
import Uploader from 'simple-uploader.js'
import elDragDialog from '@/directive/el-drag-dialog'
import { getToken } from '@/utils/auth'
import { formatSize } from '@/utils'
import FileList from './components/FileList'
import ImageCrop from './components/ImageCrop'

export default {
  name: 'Uploader',
  version: '0.1.10',
  provide() {
    return {
      uploader: this
    }
  },
  directives: { elDragDialog },
  components: {
    FileList,
    ImageCrop
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
      // 初始截取框宽度
      type: Number,
      default: 120
    },
    cropHeight: {
      // 初始截取框高度
      type: Number,
      default: 120
    },
    cropFixed: {
      // 是否开启截图框宽高固定比例
      type: Boolean,
      default: false
    },
    cropFixedRatio: {
      // 截图框的宽高比例（需要开启固定比例）
      type: Array,
      default() {
        return [1, 1]
      }
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
      showCrop: true,
      files: []
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
    },
    typeLimitTips() {
      // 格式转换成文字提示
      if (this.typeLimit.length > 0) {
        const typeTips = []
        for (const type of this.typeLimit) {
          let suffix
          switch (type) {
            case 'image':
              suffix = '图片'
              break
            case 'video':
              suffix = '视频'
              break
            case 'audio':
              suffix = '音频'
              break
            default:
              suffix = type
          }
          typeTips.push(suffix)
        }
        return typeTips.join(', ') + '格式'
      } else {
        return '不限'
      }
    },
    sizeLimitTips() {
      // 大小转换成文字提示
      if (this.sizeLimit) {
        return formatSize(this.sizeLimit)
      } else {
        return '不限'
      }
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
          message: '"' + file.name + '"的添加已被忽略',
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
    fileError(rootFile, file, message, chunk) {},
    complete(files, message) {
      console.log(files, message)
    },
    remove(file) {
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

<style lang="scss" scoped>
.el-upload {
  display: block;
  margin-top: 10px;
  .el-upload-dragger {
    width: 100%;
  }
}
.el-icon-warning {
  font-size: 67px;
  color: #c0c4cc;
  margin: 40px 0 16px;
  line-height: 50px;
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
</style>

