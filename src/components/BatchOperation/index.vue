<template>
  <div class="batch">
    <span>批量操作：</span>
    <el-button-group>
      <el-button type="default" size="small">排序</el-button>
      <el-button type="success" size="small">启用</el-button>
      <el-button type="info" size="small">停用</el-button>
      <el-button type="danger" size="small">删除</el-button>
    </el-button-group>
  </div>
</template>
<script>
export default {
  name: 'BatchOperation',
  data() {
    return {
      empty: ''
    }
  }
}
</script>
<style lang="scss" scoped>
.batch {
  font-size: 15px;
  float: right;
  margin-bottom: 20px;
}
</style>
