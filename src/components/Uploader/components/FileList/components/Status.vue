<template>
  <div class="status">
    <p v-show="row.speed">{{ row.speed | formatSpeed }}</p>
    <p>{{ row | formatStatus }}</p>
  </div>
</template>

<script>
import { formatSize, secondsToStr } from '@/utils'

export default {
  filters: {
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
        if (Number.isFinite(time) && time > 0) {
          return secondsToStr(time)
        } else if (row.progress !== 1) {
          return '正在连接'
        } else {
          return '-'
        }
      } else {
        return '等待上传'
      }
    }
  },
  props: {
    data: {
      type: Object,
      default() {
        return {
          progress: '',
          speed: '',
          timeRemaining: '',
          uploadedSize: '',
          paused: '',
          error: '',
          isUploading: '',
          isComplete: ''
        }
      }
    }
  },
  data() {
    return {
      row: this.data
    }
  }
}
</script>

<style lang="scss" scoped>
.status {
  p {
    margin: 0;
    height: 23px;
    overflow: hidden;
  }
}
</style>
