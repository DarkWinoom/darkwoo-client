<template>
  <div v-if="value" class="batch">
    <span>批量操作：</span>
    <el-button-group>
      <el-button v-if="sequence" type="default" size="small">{{ sequence }}</el-button>
      <el-button v-if="enable" type="success" size="small">{{ enable }}</el-button>
      <el-button v-if="disable" type="info" size="small">{{ disable }}</el-button>
      <el-button v-if="remove" type="danger" size="small">{{ remove }}</el-button>
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

