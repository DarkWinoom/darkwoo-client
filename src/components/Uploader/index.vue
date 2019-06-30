<template>
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
          ref="fileList"
          :target="target"
          :files="files"
          :queue-limit="queueLimit"
          :crop-open="cropOpen"
          :crop-width="cropWidth"
          :crop-height="cropHeight"
          :crop-fixed="cropFixed"
          :crop-output-quantity="cropOutputQuantity"
          :crop-output-type="cropOutputType"
          :empty-on-complete="emptyOnComplete"
          :dialog-visible="dialogVisible"
          :spark-unique="sparkUnique"
          @add-file="handleAddFile"
          @remove="handleRemove"
          @complete="complete"
        />
      </el-col>
    </el-row>
  </support-check>
</template>
<script>
import Uploader from 'simple-uploader.js'
import { getToken } from '@/utils/auth'
import SupportCheck from './components/SupportCheck'
import DropField from './components/DropField'
import FileList from './components/FileList'

export default {
  name: 'Uploader',
  version: '0.3.20',
  provide() {
    return {
      uploader: this
    }
  },
  components: {
    SupportCheck,
    DropField,
    FileList
  },
  props: {
    field: {
      // 上传文件的域
      type: String,
      default: 'upload'
    },
    target: {
      // 上传目标url（默认地址仅作为测试用）
      type: String,
      default: 'https://httpbin.org/post'
    },
    headers: {
      // 额外的请求头
      type: Object,
      default() {
        return {}
      }
    },
    query: {
      // 额外的请求参数
      // 接收两个参数：file、chunk
      type: Function,
      default() {
        return function(file, chunk) {
          return {}
        }
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
    emptyOnComplete: {
      // 为true时，会在上传成功时清空列表
      type: Boolean,
      default: false
    },
    sparkUnique: {
      // 使用spark-md5计算值来代替uniqueIdentifier
      // 在添加文件时将会自动计算，对于大体积文件需要花费一定时间（同时在计算时可能会造成浏览器卡顿）
      // 通过计算后，将会更加准确的触发快传和断点续传
      type: Boolean,
      default: true
    },
    dialogVisible: {
      // 外部控件显示与否，用于更新文件状态
      // 使用模态框显示时需要将该参数绑定其visible属性，不使用时忽略
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      loading: false,
      // 是否支持
      support: true,
      // simple-uploader 配置
      simpleUploaderOptions: {
        target: this.target,
        testChunks: true,
        checkChunkUploadedByResponse: (chunk, message) => {
          const objMessage = JSON.parse(message)
          if (objMessage.data.id) {
            return true
          }
          return (objMessage.data.skipChunks || []).indexOf(chunk.offset + 1) >= 0
        },
        // simultaneousUploads: 1,
        fileParameterName: this.field,
        successStatuses: [200, 201, 202],
        permanentErrors: [206, 404, 415, 500, 501],
        singleFile: this.queueLimit === 1,
        initialPaused: true,
        headers: (file, chunk) => {
          return {
            ...this.headers,
            'X-Token': getToken()
          }
        },
        query: this.query()
      },
      files: []
    }
  },
  created() {
    this.uploader = new Uploader(this.simpleUploaderOptions)
    this.support = this.uploader.support
    if (this.support) {
      this.uploader.on('fileAdded', this.fileAdded)
      this.uploader.on('fileRemoved', this.fileRemoved)
      this.uploader.on('filesSubmitted', this.filesSubmitted)
    }
  },
  mounted() {
    this.$nextTick(() => {
      if (this.support) {
        var accept = []
        if (this.typeLimit) {
          for (let type of this.typeLimit) {
            type = type.toLowerCase()
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
                if (type) {
                  accept.push('.' + type)
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
    complete(message) {
      this.$emit('complete', message)
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
    handleAddFile(file) {
      this.uploader.addFile(file)
    },
    handleRemove(file) {
      this.uploader.removeFile(file)
    }
  }
}
</script>
