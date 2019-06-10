<template>
  <div v-if="value" class="batch">
    <span>批量操作：</span>
    <el-button-group>
      <el-button v-show="sequence" type="default" size="small">{{ sequence }}</el-button>
      <el-button v-show="enable" type="success" size="small">{{ enable }}</el-button>
      <el-button v-show="disable" type="info" size="small">{{ disable }}</el-button>
      <el-button v-show="remove" type="danger" size="small">{{ remove }}</el-button>
    </el-button-group>
  </div>
</template>
<script>
export default {
  name: 'MenuContainerBatch',
  props: {
    value: {
      type: Boolean,
      default: false
    },
    sequence: {
      type: String,
      default: ''
    },
    enable: {
      type: String,
      default: ''
    },
    disable: {
      type: String,
      default: ''
    },
    remove: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      empty: ''
    }
  }
}
</script>
<style lang="scss" scoped>
.batch{
  display: inline-block;
  margin-left: 15px;
}
</style>

