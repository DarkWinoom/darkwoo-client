<template>
  <div>
    <template v-if="row.croped">
      <el-button title="还原" size="medium" type="primary" icon="el-icon-refresh-left" circle />
    </template>
    <template v-else-if="row.canCrop">
      <el-button
        title="裁剪"
        size="medium"
        type="primary"
        icon="el-icon-scissors"
        circle
        @click="handleCrop(row.id)"
      />
    </template>
    <template v-if="row.error">
      <el-button
        title="重试"
        size="medium"
        type="warning"
        icon="el-icon-refresh-right"
        circle
        @click="handleRetry(row.id)"
      />
    </template>
    <template v-else-if="row.isInitialization">
      <el-button
        title="开始"
        size="medium"
        type="success"
        icon="el-icon-caret-right"
        circle
        @click="handleResume(row.id)"
      />
    </template>
    <template v-else>
      <el-button
        v-if="row.paused"
        title="继续"
        size="medium"
        type="success"
        icon="el-icon-caret-right"
        circle
        @click="handleResume(row.id)"
      />
      <el-button
        v-if="!row.paused"
        title="暂停"
        size="medium"
        icon="el-icon-video-pause"
        circle
        @click="handlePause(row.id)"
      />
    </template>
    <el-button
      title="移除"
      size="medium"
      type="danger"
      icon="el-icon-delete"
      circle
      @click="handleRemove(row.id)"
    />
  </div>
</template>

<script>
export default {
  props: {
    data: {
      type: Object,
      required: true,
      default() {
        return {
          id: '',
          croped: '',
          canCrop: '',
          error: '',
          isInitialization: '',
          paused: ''
        }
      }
    }
  },
  data() {
    return {
      row: this.data
    }
  },
  methods: {
    handleCrop(id) {
      this.$emit('crop', id)
    },
    handleResume(id) {
      this.$emit('resume', id)
    },
    handlePause(id) {
      this.$emit('pause', id)
    },
    handleRetry(id) {
      this.$emit('retry', id)
    },
    handleRemove(id) {
      this.$confirm('将文件移除列表?', '系统提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.$emit('remove', id)
      })
    }
  }
}
</script>
