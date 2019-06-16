<template>
  <el-table
    v-show="list.length > 0"
    :key="key"
    v-loading="loading"
    :data="list"
    :max-height="maxHeight"
  >
    <el-table-column label="文件" min-width="250">
      <div slot-scope="scope" class="fixed">
        <div class="thumbnails">
          <template v-if="isImage(scope.row.type)">
            <el-image :src="scope.row.url" fit="contain">
              <div slot="error" class="image-slot">
                <i class="el-icon-picture-outline" />
              </div>
            </el-image>
          </template>
          <template v-else>
            <i class="el-icon-document" />
          </template>
        </div>
        <div class="information">
          <p class="name">
            <span>{{ scope.row.name }}</span>
          </p>
          <el-progress
            :text-inside="true"
            :stroke-width="15"
            :percentage="scope.row | formatProgress"
            color="#67C23A"
          />
          <p class="size">
            <el-tag size="mini">{{ scope.row.name | formatType }}</el-tag>
            <span>{{ scope.row.uploadedSize | formatSize }}</span>
            /
            <span>{{ scope.row.size | formatSize }}</span>
            <!-- <span v-show="scope.row.croped" class="crop">（已裁剪）</span> -->
          </p>
        </div>
      </div>
    </el-table-column>
    <el-table-column label="状态" width="120">
      <div slot-scope="scope" class="fixed">
        <div class="status">
          <p v-show="scope.row.speed">{{ scope.row.speed | formatSpeed }}</p>
          <p>{{ scope.row | formatStatus }}</p>
        </div>
      </div>
    </el-table-column>
    <el-table-column width="180" align="right">
      <div v-show="!scope.row.isComplete" slot-scope="scope">
        <template v-if="scope.row.croped">
          <el-tooltip class="item" effect="dark" content="还原图片" placement="top">
            <el-button size="medium" type="primary" icon="el-icon-refresh-left" circle />
          </el-tooltip>
        </template>
        <template v-else-if="scope.row.canCrop">
          <el-tooltip class="item" effect="dark" content="图片裁剪" placement="top">
            <el-button
              size="medium"
              type="primary"
              icon="el-icon-scissors"
              circle
              @click="handleCropStart()"
            />
          </el-tooltip>
        </template>
        <template v-if="scope.row.error">
          <el-tooltip class="item" effect="dark" content="重试" placement="top">
            <el-button size="medium" type="warning" icon="el-icon-refresh-right" circle />
          </el-tooltip>
        </template>
        <template v-else-if="scope.row.isInitialization">
          <el-tooltip class="item" effect="dark" content="开始上传" placement="top">
            <el-button size="medium" type="success" icon="el-icon-caret-right" circle />
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
            />
          </el-tooltip>
          <el-tooltip class="item" effect="dark" content="暂停" placement="top">
            <el-button v-if="!scope.row.paused" size="medium" icon="el-icon-video-pause" circle />
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
</template>

<script>
import {
  formatSize,
  secondsToStr
  // data2blob
} from '../utils'

export default {
  name: 'UploaderFileList',
  filters: {
    formatSize(size) {
      return formatSize(size)
    },
    formatType(name) {
      return name.substr(name.lastIndexOf('.') + 1).toUpperCase()
    },
    formatProgress(row) {
      // 上传进度文本
      let progress = Math.floor(row.progress * 10000) / 100
      if (progress >= 100 && !row.isComplete) {
        progress = 99
      }
      return progress
    },
    formatSpeed(speed) {
      // 速度文本
      if (speed > 0) {
        return `${formatSize(speed)} / 秒`
      } else {
        return ''
      }
    },
    formatStatus(row) {
      // 剩余时间文本（状态）
      if (row.isComplete) {
        return '上传成功'
      } else if (row.error) {
        return '上传失败'
      } else if (row.paused) {
        return '已暂停'
      } else if (row.isUploading) {
        const time = row.timeRemaining
        if (Number.isFinite(time)) {
          return secondsToStr(time)
        } else {
          return '正在上传'
        }
      } else {
        return '等待上传'
      }
    }
  },
  props: {
    files: {
      type: Array,
      default() {
        return []
      }
    },
    cropOption: {
      type: Object,
      default() {
        return {}
      }
    }
  },
  data() {
    return {
      loading: false,
      maxHeight: 0,
      key: 0,
      list: [],
      lines: 4 // 显示的文件行数
    }
  },
  computed: {
    cropOpen() {
      // 是否开启了裁剪功能
      if (this.cropOption) {
        return this.cropOption.open === true
      } else {
        return false
      }
    }
  },
  watch: {
    files(value) {
      if (value.length > 0) {
        for (const file of value) {
          this.fileInit(file)
        }
      }
    }
  },
  created() {
    this.maxHeight = this.lines * 93 + 48
  },
  methods: {
    fileInit(rowFile) {
      console.log(rowFile)
      const files_id = this.list.map(item => item.id)
      if (!files_id.includes(rowFile.id)) {
        const file = {
          file: rowFile,
          id: rowFile.id,
          type: rowFile.fileType,
          name: rowFile.name,
          size: rowFile.size,
          progress: 0,
          speed: 0,
          uploadedSize: 0, // 已上传的大小
          paused: false, // 是否已暂停
          error: false, // 是否出错
          croped: false, // 是否已经被裁剪
          canCrop: this.cropOpen && this.isImage(rowFile.fileType), // 是否显示裁剪按钮
          isInitialization: true, // 是否新加入的文件（用于显示“开始”按钮）
          isComplete: false, // 是否已上传完毕
          isUploading: false // 是否正在上传
        }
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
      }
    },
    isImage(type) {
      return type.indexOf('image') !== -1
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
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.thumbnails {
  position: absolute;
  height: 30px;
  .el-image {
    position: relative;
    top: 3px;
    width: 30px;
    height: 30px;
  }
  i {
    position: relative;
    top: 3px;
    font-size: 30px;
  }
}
.information {
  padding-left: 40px;
  .el-progress {
    margin: 3px 0;
  }
  p {
    margin: 0;
    height: 23px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    &.name {
      font-size: 16px;
      color: #333;
    }
    &.size {
      font-size: 14px;
      color: #aaa;
    }
  }
}
.status {
  p {
    margin: 0;
    height: 23px;
    overflow: hidden;
  }
}
.crop {
  color: #f56c6c;
}
</style>
