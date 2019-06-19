<template>
  <div class="upload-pane">
    <div class="button">
      <el-button
        :disabled="!showStart"
        type="success"
        icon="el-icon-upload2"
        size="small"
        plain
        @click="handleStartAll"
      >全部开始</el-button>
      <el-button
        :disabled="!showPause"
        type="default"
        icon="el-icon-video-pause"
        size="small"
        plain
        @click="handlePauseAll"
      >全部暂停</el-button>
      <el-button
        :disabled="!showClear"
        type="danger"
        icon="el-icon-delete"
        size="small"
        plain
        @click="handleClear"
      >清空</el-button>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    list: {
      type: Array,
      default() {
        return []
      }
    }
  },
  data() {
    return {
      empty: ''
    }
  },
  computed: {
    showStart() {
      // 开始按钮可用条件：列表非空，并且至少有一项没有在上传中或者上传成功
      if (this.list.length > 0) {
        let started = 0
        for (const item of this.list) {
          if (item.isUploading || item.isComplete) {
            started++
          }
        }
        return this.list.length !== started
      }
      return false
    },
    showPause() {
      // 暂停按钮可用条件：列表非空，并且至少有一项正在上传中
      if (this.list.length > 0) {
        let uploading = 0
        for (const item of this.list) {
          if (item.isUploading) {
            uploading++
          }
        }
        return uploading > 0
      }
      return false
    },
    showClear() {
      // 清空按钮可用条件：列表非空
      return this.list.length > 0
    }
  },
  methods: {
    handleStartAll() {
      this.$emit('start')
    },
    handlePauseAll() {
      this.$emit('pause')
    },
    handleClear() {
      this.$emit('clear')
    }
  }
}
</script>

<style lang="scss" scoped>
.upload-pane {
  position: relative;
  z-index: 2;
  .button {
    position: absolute;
    top: 0;
    right: 0;
  }
}
</style>
