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
            <template v-if="option.queueSize > 0">
              <p v-if="option.queueSize === 1">请选择单一文件</p>
              <p v-else>数量限制：{{ option.queueSize }}</p>
            </template>
            <p>格式限制：{{ typeLimitTips }}</p>
            <p>尺寸建议：120 x 120</p>
            <p>单文件大小限制：{{ sizeLimitTips }}</p>
          </div>
        </el-col>
        <el-col :span="16">
          <el-tabs class="upload-pane">
            <el-tab-pane :label="'上传列表 (' + files.length + ')'">
              <file-list :files="files" :crop-option="option.crop" @remove="remove" />
            </el-tab-pane>
          </el-tabs>
        </el-col>
      </el-row>
      <div slot="footer" class="dialog-footer">
        <el-button type="success" size="medium" icon="el-icon-upload2" :loading="loading" plain>全部开始</el-button>
        <el-button type="danger" icon="el-icon-delete" size="medium" plain>清空</el-button>
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
import { formatSize } from './utils'
import FileList from './components/FileList'

export default {
  name: 'Uploader',
  version: '0.1.3',
  provide() {
    return {
      uploader: this
    }
  },
  directives: { elDragDialog },
  components: {
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
      type: String,
      default: 'upload'
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

          // 单文件大小限制（默认不限）
          sizeLimit: 0,

          // 上传文件格式限制，支持扩展名与image、video、audio（默认不限）
          // example：['zip','image','pdf','doc','docx']
          typeLimit: ['image'],

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
      // 上传框总宽度
      width: '920px',
      // 显示的标题
      showTitle: '',
      // 是否支持
      support: true,
      // simple-uploader 配置
      simpleUploaderOption: {
        target: 'https://httpbin.org/post',
        testChunks: false,
        fileParameterName: this.field,
        successStatuses: [200, 201, 202],
        permanentErrors: [206, 404, 415, 500, 501],
        singleFile: this.option.queueSize === 1,
        headers: (file, chunk) => {
          return this.option.headers
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
      if (this.option.typeLimit.length > 0) {
        const typeTips = []
        for (const type of this.option.typeLimit) {
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
      if (this.option.sizeLimit) {
        return formatSize(this.option.sizeLimit)
      } else {
        return '不限'
      }
    }
  },
  created() {
    this.uploader = new Uploader(this.simpleUploaderOption)
    this.support = this.uploader.support
    if (this.support) {
      this.uploader.on('fileAdded', this.fileAdded)
      this.uploader.on('fileRemoved', this.fileRemoved)
      this.uploader.on('filesSubmitted', this.filesSubmitted)
      this.uploader.on('fileError', this.fileError)
      this.uploader.on('complete', this.complete)
    }
  },
  mounted() {
    this.$nextTick(() => {
      if (this.support) {
        var accept = []
        if (this.option.typeLimit) {
          for (const type of this.option.typeLimit) {
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
      this.uploader.off('complete', this.complete)
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
      if (this.files.length >= this.option.queueSize) {
        this.ignoreNotify(file, '文件数量超过限制')
        return false
      } else if (file.name.lastIndexOf('.') === -1) {
        this.ignoreNotify(file, '文件格式有误')
        return false
      } else if (this.option.typeLimit.length > 0) {
        const suffix = file.name
          .substring(file.name.lastIndexOf('.') + 1, file.name.length)
          .toLowerCase()
        let access = false
        for (const type of this.option.typeLimit) {
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
      if (this.option.sizeLimit && file.size > this.option.sizeLimit) {
        this.ignoreNotify(file, '文件大小超过限制')
        return false
      }
    },
    fileRemoved(file) {
      const index = this.files.findIndex(item => item.id === file.id)
      this.files.splice(index, 1)
    },
    filesSubmitted(files, fileList, event) {
      if (this.files.length + files.length > this.option.queueSize) {
        const less = this.option.queueSize - this.files.length
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
      this.$emit('close')
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

