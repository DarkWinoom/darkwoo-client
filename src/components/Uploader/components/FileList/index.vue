<template>
  <div>
    <control-button
      v-show="queueLimit !== 1"
      :show-start="!isComplete"
      :show-clear="list.length > 0"
      @start="handleStartAll"
      @clear="handleClear"
    />
    <el-tabs>
      <el-tab-pane :label="'上传列表 (' + files.length + ')'">
        <el-table v-show="list.length > 0" v-loading="loading" :data="list" :max-height="maxHeight">
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
              <template v-if="scope.row.croped">
                <el-tooltip class="item" effect="dark" content="还原图片" placement="top">
                  <el-button size="medium" type="primary" icon="el-icon-refresh-left" circle />
                </el-tooltip>
              </template>
              <template v-else-if="scope.row.canCrop">
                <el-tooltip class="item" effect="dark" content="图片裁剪" placement="top">
                  <el-button size="medium" type="primary" icon="el-icon-scissors" circle />
                </el-tooltip>
              </template>
              <template v-if="scope.row.error">
                <el-tooltip class="item" effect="dark" content="重试" placement="top">
                  <el-button
                    size="medium"
                    type="warning"
                    icon="el-icon-refresh-right"
                    circle
                    @click="handleRetry(scope.row)"
                  />
                </el-tooltip>
              </template>
              <template v-else-if="scope.row.isInitialization">
                <el-tooltip class="item" effect="dark" content="开始上传" placement="top">
                  <el-button
                    size="medium"
                    type="success"
                    icon="el-icon-caret-right"
                    circle
                    @click="handleResume(scope.row)"
                  />
                </el-tooltip>
              </template>
              <template v-else>
                <el-tooltip class="item" effect="dark" content="继续上传" placement="top">
                  <el-button
                    v-if="scope.row.paused"
                    size="medium"
                    type="success"
                    icon="el-icon-caret-right"
                    circle
                    @click="handleResume(scope.row)"
                  />
                </el-tooltip>
                <el-tooltip class="item" effect="dark" content="暂停" placement="top">
                  <el-button
                    v-if="!scope.row.paused"
                    size="medium"
                    icon="el-icon-video-pause"
                    circle
                    @click="handlePause(scope.row)"
                  />
                </el-tooltip>
              </template>
              <el-tooltip class="item" effect="dark" content="移除队列" placement="top">
                <el-button
                  size="medium"
                  type="danger"
                  icon="el-icon-delete"
                  circle
                  @click="handleRemove(scope.row)"
                />
              </el-tooltip>
            </div>
          </el-table-column>
        </el-table>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
import ControlButton from './components/Button'
import FileInformation from './components/Information'
import FileStatus from './components/Status'

export default {
  name: 'UploaderFileList',
  components: { ControlButton, FileInformation, FileStatus },
  props: {
    value: {
      // 隐藏 / 显示控件（状态）
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
      events: [
        'fileProgress',
        'fileSuccess' /* , 'fileComplete' */,
        'fileError'
      ],
      handlers: {},
      errors: [], // 出错的file.id，用以过滤错误提示
      fileIncrease: 0, // 文件列表变动次数（新增或删除）
      list: [],
      lines: 4, // 显示的文件行数
      completeLock: false
      // 队列完成锁。某些情况下complete()方法会被多次调用，因此加入了一个锁机制
      // 解锁事件：加入新文件、开始 & 暂停下载、删除下载
    }
  },
  computed: {
    isComplete() {
      // 列表中的文件是否全部上传完毕（若列表为空返回true）
      let complete = true
      for (const row of this.list) {
        if (row.isComplete === false || row.error === true) {
          complete = false
        }
      }
      return complete
    }
  },
  watch: {
    value() {
      for (const file of this.files) {
        this._actionCheck(file)
      }
    },
    files(value) {
      for (const file of value) {
        this.fileIncrease++
        this.fileInit(file, this.fileIncrease)
      }
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
      this.completeLock = false
      const files_id = this.list.map(item => item.id)
      if (!files_id.includes(rowFile.id)) {
        const file = {
          file: rowFile.file,
          id: rowFile.id,
          type: rowFile.fileType,
          name: rowFile.name,
          size: rowFile.size,
          progress: 0,
          speed: 0,
          timeRemaining: 0,
          uploadedSize: 0, // 已上传的大小
          paused: false, // 是否已暂停
          error: false, // 是否出错
          croped: false, // 是否已经被裁剪
          canCrop: this.cropOpen && this.isImage(rowFile.fileType), // 是否显示裁剪按钮
          isInitialization: true, // 是否新加入的文件（用于显示“开始”按钮）
          isComplete: false, // 是否已上传完毕
          isUploading: false // 是否正在上传
        }
        const eventHandler = event => {
          this.handlers[event] = (...args) => {
            this.fileEventsHandler(event, args)
          }
          return this.handlers[event]
        }
        this.events.forEach(event => {
          rowFile.uploader.on(event, eventHandler(event))
        })
        if (this.isImage(rowFile.fileType)) {
          const fr = new FileReader()
          fr.onload = e => {
            file.url = fr.result
            if (rowFile.uploader.opts.singleFile === true) {
              // 设定了只上传单一文件
              this.list = [file]
            } else {
              this.list.push(file)
            }
          }
          fr.readAsDataURL(rowFile.file)
        } else {
          if (rowFile.uploader.opts.singleFile === true) {
            // 设定了只上传单一文件
            this.list = [file]
          } else {
            this.list.push(file)
          }
        }
        if (files_id.length === 0 && this.fileIncrease <= 1) {
          rowFile.uploader.on('complete', () => {
            this.complete()
          })
        }
      }
    },
    isImage(type) {
      return type.indexOf('image') !== -1
    },
    fileEventsHandler(event, args) {
      if (args[1] === args[0]) {
        /* if (event === 'fileSuccess') {
          // this.callback.push(JSON.parse(args[2]))
          return
        } */
        this[`_${event}`].apply(this, args)
      }
    },
    handleResume(row) {
      // 继续 & 开始下载
      row.canCrop = false // 开始后禁止裁剪
      const file = this._getFile(row.id)
      file.resume()
      this._actionCheck(file)
      this.completeLock = false
    },
    handlePause(row) {
      // 暂停
      const file = this._getFile(row.id)
      file.pause()
      this._actionCheck(file)
      this._fileProgress(file)
    },
    handleRetry(row) {
      // 重试
      const file = this._getFile(row.id)
      file.retry()
      this._actionCheck(file)
    },
    handleRemove(row) {
      this.$confirm('将文件“' + row.name + '”移除列表?', '系统提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        const index = this.list.findIndex(item => item.id === row.id)
        this.list.splice(index, 1)
        this.$emit('remove', row.file)
        this.completeLock = false
        if (this.isComplete) {
          this.complete()
        }
      })
    },
    complete() {
      // 列表中文件全部上传完成后回调
      if (this.completeLock) {
        return false
      }
      this.completeLock = true
      console.log('complete')
    },
    _getFile(rowId, key = 'id') {
      for (const file of this.files) {
        if (file[key] === rowId) {
          return file
        }
      }
      return false
    },
    _getRow(fileId, key = 'id') {
      for (const row of this.list) {
        if (row[key] === fileId) {
          return row
        }
      }
      return false
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
      const json = JSON.parse(message)
      if (json.data) {
        return this._fileSuccess(rootFile, file, message, chunk)
      }
      this._fileProgress(file)
      const row = this._getRow(file.id)
      row.error = true
      row.isComplete = false
      row.isUploading = false
      if (!this.errors.includes(file.id)) {
        setTimeout(() => {
          this.$notify({
            title: '文件上传失败',
            message: typeof message === 'string' ? message : '未知错误',
            type: 'warning',
            duration: 6000
          })
        }, 10)
        this.errors.push(file.id)
      }
    },
    handleStartAll() {
      // 全部开始
      if (this.isComplete) return true
      for (const row of this.list) {
        this.handleResume(row)
      }
    },
    handleClear() {
      // 清空上传列表（提示）
      this.$confirm('清空上传列表?', '系统提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        const files = this.files.concat()
        for (const file of files) {
          this.$emit('remove', file)
        }
        this.list = []
        this.errors = []
        this.loading = false
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.crop {
  color: #f56c6c;
}
</style>