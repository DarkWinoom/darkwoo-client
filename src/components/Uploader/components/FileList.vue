<template>
  <el-table
    v-show="files.length > 0"
    :key="key"
    v-loading="loading"
    :data="files"
    :max-height="maxHeight"
  >
    <el-table-column label="文件" min-width="200">
      <div slot-scope="scope" class="fixed">
        <div class="thumbnails">
          <template v-if="isImage(scope.row.type)">
            <el-image :src="scope.row.url" fit="contain" />
          </template>
          <template v-else>
            <i class="el-icon-document" />
          </template>
        </div>
        <div class="information">
          <p class="name">
            <span>{{ scope.row.name }}</span>
          </p>
          <p class="size">
            <span>{{ scope.row.size | formatSize }}</span>
            <span v-show="scope.row.croped" class="crop">（已裁剪）</span>
          </p>
        </div>
      </div>
    </el-table-column>
    <el-table-column label="进度" width="100">
      <div slot-scope="scope" class="fixed">{{ scope.row | progressText }}</div>
    </el-table-column>
    <el-table-column label="状态" width="120">
      <div slot-scope="scope" class="fixed">
        <div class="status">
          <p>{{ scope.row.averageSpeed | speedText }}</p>
          <p>{{ scope.row | timeRemainingText }}</p>
        </div>
      </div>
    </el-table-column>
    <el-table-column label="操作" width="220" align="center">
      <div v-show="!scope.row.isComplete" slot-scope="scope">
        <template v-show="scope.row.canCrop" class="crop-opr">
          <el-button size="mini" type="primary" @click="handleCropStart()">裁剪</el-button>
        </template>
        <template v-if="scope.row.error">
          <el-button size="mini" type="warning">重试</el-button>
        </template>
        <template v-else-if="scope.row.isInitialization">
          <el-button size="mini" type="success">开始</el-button>
        </template>
        <template v-else>
          <el-button v-if="scope.row.paused" size="mini" type="success">继续</el-button>
          <el-button v-if="!scope.row.paused" size="mini">暂停</el-button>
        </template>
        <el-button size="mini" type="danger">移除</el-button>
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
    progressText(row) {
      // 上传进度文本
      let progress = Math.floor(row.progress * 10000) / 100
      if (progress >= 100 && !row.isComplete) {
        progress = 99
      }
      return progress ? `${progress}%` : '-'
    },
    speedText(speed) {
      // 速度文本
      if (speed > 0) {
        return `${formatSize(speed)} / 秒`
      } else {
        return '-'
      }
    },
    timeRemainingText(row) {
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
  data() {
    return {
      loading: false,
      key: 0,
      lines: 2, // 显示的文件行数
      files: []
    }
  },
  created() {
    this.fileInit()
    this.maxHeight = this.lines * 71 + 48
  },
  methods: {
    fileInit() {
      const files1 = {
        id: 1,
        type: 'image/png',
        url:
          'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif?imageView2/1/w/80/h/80',
        name: '图片.png',
        size: 2145225214,
        progress: 0.1565,
        averageSpeed: 4111415,
        timeRemaining: 422,
        paused: false, // 是否已暂停
        error: false, // 是否出错
        croped: true, // 是否已经被裁剪
        canCrop: true, // 是否显示裁剪按钮
        isInitialization: true, // 是否新加入的文件（用于显示“开始”按钮）
        isComplete: false, // 是否已上传完毕
        isUploading: true // 是否正在上传
      }
      const files2 = {
        id: 2,
        type: 'application/docx',
        name: '文档.docx',
        size: 2145225214,
        progress: 0.1565,
        averageSpeed: 4111415,
        timeRemaining: 422,
        paused: false, // 是否已暂停
        error: false, // 是否出错
        croped: false, // 是否已经被裁剪
        canCrop: false, // 是否显示裁剪按钮
        isInitialization: true, // 是否新加入的文件（用于显示“开始”按钮）
        isComplete: false, // 是否已上传完毕
        isUploading: true // 是否正在上传
      }
      for (let i = 0; i < 10; i++) {
        this.files.push(files1)
        this.files.push(files2)
      }
    },
    isImage(type) {
      return type.indexOf('image') !== -1
    }
  }
}
</script>

<style lang="scss" scoped>
.thumbnails {
  float: left;
  height: 46px;
  .el-image {
    width: 46px;
    height: 46px;
  }
  i{
    font-size: 23px;
  }
}
.information {
  float: left;
  margin-left: 10px;
  p {
    margin: 0;
    height: 23px;
    overflow: hidden;
    &.name {
      font-size: 16px;
      color: #333;
    }
    &.size {
      color: #999;
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
.crop-opr {
  margin-bottom: 10px;
}
</style>
