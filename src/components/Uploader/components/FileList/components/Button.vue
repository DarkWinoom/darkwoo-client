<template>
  <div class="upload-pane">
    <div class="button">
      <el-button
        v-show="showStart"
        type="success"
        icon="el-icon-upload2"
        size="small"
        plain
        @click="handleStartAll"
      >全部开始</el-button>
      <el-button
        v-show="showClear"
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
    showStart: {
      type: Boolean,
      default: false
    },
    showClear: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    handleStartAll() {
      this.$emit('start')
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
