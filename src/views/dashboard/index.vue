<template>
  <div class="app-container">
    <div class="welcome">
      <div class="title">下午好 <b>{{ name }}</b>，欢迎使用本管理系统</div>
      <div class="content">
        请点击左侧功能选项卡进行操作
        <input type="text" :value="title">
        <input type="file" @change="change">
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
import * as qiniu from 'qiniu-js'

export default {
  name: 'Dashboard',
  data() {
    return {
      token: '',
      title: 'avatar/01.png'
    }
  },
  computed: {
    ...mapGetters([
      'name'
    ])
  },
  mounted() {
    getToken((token) => {
      axios({
        baseURL: '/local-api',
        headers: {
          'X-Token': token,
          'X-Version': 'v1'
        },
        /* method: 'get',
        url: '/access/upload/token' */
        method: 'get',
        url: '/access/authentication',
        params: {
          name: 'dashboard'
        }
      }).then(response => {
        this.token = response.data.data.token
        // console.log(response.data.data, response.data.message)
      })
    })
  },
  methods: {
    change(e) {
      const file = e.target.files[0]
      const observable = qiniu.upload(file, 'avatar/06.png', this.token)
      observable.subscribe((res) => {
        console.log(res.total.percent)
      }, (res) => {
        console.log('error', res)
      }, (res) => {
        console.log('complete', res)
      })
    }
  }
}
function getToken(callback = (token) => {}) {
  axios({
    baseURL: '/local-api',
    headers: {
      'X-Version': 'v1'
    },
    method: 'post',
    url: '/access/login',
    data: {
      passport: 'admin',
      password: 'admin'
    }
  }).then(server => {
    const token = server.data.data.token
    callback(token)
  })
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
