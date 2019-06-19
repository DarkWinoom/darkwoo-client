<template>
  <div>
    <template v-if="row.croped">
      <el-tooltip class="item" effect="dark" content="还原图片" placement="top">
        <el-button size="medium" type="primary" icon="el-icon-refresh-left" circle />
      </el-tooltip>
    </template>
    <template v-else-if="row.canCrop">
      <el-tooltip class="item" effect="dark" content="图片裁剪" placement="top">
        <el-button size="medium" type="primary" icon="el-icon-scissors" circle />
      </el-tooltip>
    </template>
    <template v-if="row.error">
      <el-tooltip class="item" effect="dark" content="重试" placement="top">
        <el-button
          size="medium"
          type="warning"
          icon="el-icon-refresh-right"
          circle
          @click="handleRetry(row.id)"
        />
      </el-tooltip>
    </template>
    <template v-else-if="row.isInitialization">
      <el-tooltip class="item" effect="dark" content="开始上传" placement="top">
        <el-button
          size="medium"
          type="success"
          icon="el-icon-caret-right"
          circle
          @click="handleResume(row.id)"
        />
      </el-tooltip>
    </template>
    <template v-else>
      <el-tooltip class="item" effect="dark" content="继续上传" placement="top">
        <el-button
          v-if="row.paused"
          size="medium"
          type="success"
          icon="el-icon-caret-right"
          circle
          @click="handleResume(row.id)"
        />
      </el-tooltip>
      <el-tooltip class="item" effect="dark" content="暂停" placement="top">
        <el-button
          v-if="!row.paused"
          size="medium"
          icon="el-icon-video-pause"
          circle
          @click="handlePause(row.id)"
        />
      </el-tooltip>
    </template>
    <el-tooltip class="item" effect="dark" content="移除队列" placement="top">
      <el-button
        size="medium"
        type="danger"
        icon="el-icon-delete"
        circle
        @click="handleRemove(row.id)"
      />
    </el-tooltip>
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
      this.$emit('remove', id)
    }
  }
}
</script>
