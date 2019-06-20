<template>
  <div>
    <file-control
      v-show="queueLimit !== 1"
      :list="list"
      @start="handleStartAll"
      @pause="handlePauseAll"
      @clear="handleClear"
    />
    <el-tabs>
      <el-tab-pane :label="'上传列表 (' + files.length + ')'">
        <el-table
          v-show="list.length > 0"
          :key="key"
          v-loading="loading"
          :data="list"
          :max-height="maxHeight"
        >
          <el-table-column label="文件" min-width="250">
            <template slot-scope="scope">
              <file-information :data="scope.row" />
            </template>
          </el-table-column>
          <el-table-column label="状态" width="120">
            <template slot-scope="scope">
              <file-status :data="scope.row" />
            </template>
          </el-table-column>
          <el-table-column width="160" align="right">
            <div v-show="!scope.row.isComplete" slot-scope="scope">
              <file-button
                :data="scope.row"
                @retry="handleRetry"
                @resume="handleResume"
                @pause="handlePause"
                @remove="handleRemove"
              />
            </div>
          </el-table-column>
        </el-table>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
import FileControl from './components/Control'
import FileInformation from './components/Information'
import FileStatus from './components/Status'
import FileButton from './components/Button'

export default {
  name: 'UploaderFileList',
  components: { FileControl, FileInformation, FileStatus, FileButton },
  props: {
    // 监视上传框是否显示（用来更新状态）
    frameShow: {
      type: Boolean,
      default: false
    },
    files: {
      type: Array,
      default() {
        return []
      }
    },
    queueLimit: {
      type: Number,
      default: 0
    },
    cropOpen: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      loading: false,
      maxHeight: 0,
      key: 0, // 修改key可以强制刷新表格，用来解决删除文件后表格不更新的问题
      events: [
        'fileProgress',
        'fileSuccess' /* , 'fileComplete' */,
        'fileError',
        'complete'
      ],
      handlers: {},
      errors: [], // 出错的file.id，用以过滤错误提示
      index: 0, // 文件索引（按新增顺序）
      list: [],
      lines: 4, // 显示的文件行数
      completeLock: false
      // 某些情况下complete()方法会被多次调用，加入锁可保证只会执行一次
    }
  },
  computed: {
    isComplete() {
      // 列表中的文件是否全部上传完毕（若列表为空返回true）
      let complete = false
      if (this.list.length > 0) {
        complete = true
        for (const row of this.list) {
          if (row.isComplete === false || row.error === true) {
            complete = false
          }
        }
      }
      return complete
    }
  },
  watch: {
    frameShow() {
      for (const file of this.files) {
        this._actionCheck(file)
      }
    },
    files(value) {
      for (const file of value) {
        this.index++
        this.fileInit(file, this.index)
      }
      // 根据文件的添加顺序重新排列
      this.list.sort((a, b) => {
        return a.queue - b.queue
      })
    }
  },
  created() {
    this.maxHeight = this.lines * 93 + 48
  },
  destroyed() {
    this.events.forEach(event => {
      for (const file of this.files) {
        file.uploader.off(event, this.handlers[event])
      }
    })
  },
  methods: {
    fileInit(rowFile, index) {
      const files_id = this.list.map(item => item.id)
      if (!files_id.includes(rowFile.id)) {
        const isImage = rowFile.fileType.indexOf('image') !== -1
        const file = {
          queue: index,
          file: rowFile.file,
          id: rowFile.id, // 原文件列表和新生成的文件列表通过id进行对应
          type: rowFile.fileType,
          url: '',
          name: rowFile.name,
          size: rowFile.size,
          progress: 0,
          speed: 0,
          timeRemaining: 0,
          uploadedSize: 0, // 已上传的大小
          paused: false, // 是否已暂停
          error: false, // 是否出错
          croped: false, // 是否已经被裁剪
          canCrop: this.cropOpen && isImage, // 是否显示裁剪按钮
          isRealImage: false, // 是否是真实的图片
          isInitialization: true, // 是否新加入的文件（用于显示“开始”按钮）
          isComplete: false, // 是否已上传完毕
          isUploading: false // 是否正在上传
        }
        const eventHandler = event => {
          this.handlers[event] = (...args) => {
            if (args[1] === args[0]) {
              this[`_${event}`].apply(this, args)
            }
          }
          return this.handlers[event]
        }
        this.events.forEach(event => {
          rowFile.uploader.on(event, eventHandler(event))
        })
        if (rowFile.uploader.opts.singleFile === true) {
          // 设定了只上传单一文件
          this.list = [file]
        } else {
          this.list.push(file)
        }
        if (isImage) {
          const fr = new FileReader()
          fr.onload = e => {
            const row = this._getRow(rowFile.id)
            row.url = fr.result
            row.isRealImage = true
          }
          fr.readAsDataURL(rowFile.file)
        }
      }
    },
    handleResume(id) {
      // 继续 & 开始下载
      const row = this._getRow(id)
      const file = this._getFile(id)
      row.canCrop = false // 开始后禁止裁剪
      file.resume()
      this._actionCheck(file)
    },
    handlePause(id) {
      // 暂停
      const file = this._getFile(id)
      file.pause()
      this._actionCheck(file)
      this._fileProgress(file)
    },
    handleRetry(id) {
      // 重试
      const file = this._getFile(id)
      file.retry()
      this._actionCheck(file)
    },
    handleRemove(id) {
      const file = this._getFile(id)
      const index = this.list.findIndex(item => item.id === id)
      this.list.splice(index, 1)
      this.$emit('remove', file)
      this.key++
      if (this.isComplete) {
        this.complete()
      }
    },
    _getFile(value, field = 'id') {
      return this.files.find(item => {
        return item[field] === value
      })
    },
    _getRow(value, field = 'id') {
      return this.list.find(item => {
        return item[field] === value
      })
    },
    _actionCheck(file) {
      const row = this._getRow(file.id)
      row.isInitialization = false
      row.paused = file.paused
      row.error = file.error
      row.isUploading = file.isUploading()
    },
    _fileProgress(file) {
      const row = this._getRow(file.id)
      row.progress = file.progress()
      row.speed = file.averageSpeed
      row.timeRemaining = file.timeRemaining()
      row.uploadedSize = file.sizeUploaded()
      this._actionCheck(file)
    },
    _fileSuccess(file, files, message, chunk) {
      this._fileProgress(file)
      const row = this._getRow(file.id)
      row.error = false
      row.isComplete = true
      row.isUploading = false
    },
    _fileError(rootFile, file, message, chunk) {
      // 上传失败
      console.log(rootFile, message)
    },
    _complete() {
      // 列表中文件全部上传完成后回调
      console.log('complete try')
      if (this.completeLock) {
        return false
      }
      this.completeLock = true
      console.log('completed!')
    },
    handleStartAll() {
      // 全部开始
      if (this.isComplete) return true
      for (const row of this.list) {
        this.handleResume(row.id)
      }
    },
    handlePauseAll() {
      // 全部暂停
      if (this.isComplete) return true
      for (const row of this.list) {
        this.handlePause(row.id)
      }
    },
    handleClear() {
      // 清空上传列表
      const files = this.files.concat()
      for (const file of files) {
        this.$emit('remove', file)
      }
      this.list = []
      this.errors = []
      this.loading = false
    }
  }
}
</script>
