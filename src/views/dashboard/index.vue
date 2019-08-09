<template>
  <div class="app-container">
    <div class="welcome">
      <div class="title">下午好 <b>{{ name }}</b>，欢迎使用本管理系统</div>
      <div class="content">
        请点击左侧功能选项卡进行操作
      </div>
    </div>
    <el-row :gutter="20">
      <el-col :span="12">
        <el-card class="dashboard-card" shadow="always">
          <div slot="header" class="header">访问统计</div>
          占位符
        </el-card>
      </el-col>
      <el-col :span="12">
        <el-card class="dashboard-card" shadow="always">
          <div slot="header" class="header">数据统计</div>
          占位符
        </el-card>
      </el-col>
      <el-col :span="24">
        <el-card class="dashboard-card" shadow="always">
          占位符
        </el-card>
      </el-col>
      <el-col :span="16">
        <el-card class="dashboard-card" shadow="always">
          <div slot="header" class="header">消息提醒</div>
          占位符
        </el-card>
      </el-col>
      <el-col :span="8">
        <el-card class="dashboard-card" shadow="always">
          <div slot="header" class="header">系统公告</div>
          占位符
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import axios from 'axios'

export default {
  name: 'Dashboard',
  computed: {
    ...mapGetters([
      'name'
    ])
  },
  mounted() {
    /* const service = axios.create({
      baseURL: '/local-api',
      timeout: 5000
    }) */
    axios({
      baseURL: '/local-api',
      method: 'post',
      url: '/access/login',
      data: {
        passport: 'admin',
        password: 'admin'
      }
    }).then(response => {
      console.log(response.data)
    })
  }
}
</script>

<style lang="scss">
.dashboard-card{
  .el-card__header{
    padding: 0;
    .header{
      position: relative;
      padding: 10px 15px;
    }
  }
  .el-card__body{
    font-size: 15px;
    padding: 15px;
  }
}
</style>
<style lang="scss" scoped>
.el-col{
  margin-bottom: 20px;
}
.welcome{
  position: relative;
  font-size: 14px;
  margin-bottom: 20px;
  padding-bottom: 10px;
  border-bottom: 1px solid #ebeef5;
  .content{
    margin-top: 5px;
    color: #909399;
  }
  .guide{
    position: absolute;
    right: 0;
    bottom: 10px;
  }
}
</style>
