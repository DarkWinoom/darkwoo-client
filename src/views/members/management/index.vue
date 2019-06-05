<template>
  <div class="app-container">
    <menu-container :batch="batch">
      <template slot="left-menu">
        <el-input
          v-model="listQuery.keyword"
          clearable
          class="item"
          size="small"
          placeholder="请输入用户信息关键字"
        >
          <el-button slot="append" icon="el-icon-search" size="small">搜索</el-button>
        </el-input>
      </template>
    </menu-container>
    <el-table ref="mainTable" v-loading="loading" :data="list" border stripe tooltip-effect="dark">
      <el-table-column type="selection" align="center" width="55" />
      <el-table-column label="用户名" align="center" width="120">
        <template slot-scope="scope">{{ scope.row.passport }}</template>
      </el-table-column>
      <el-table-column label="账号信息" min-width="200">
        <template slot-scope="scope">
          <div class="box">
            <div v-show="scope.row.avatar" class="avatar">
              <el-image :src="scope.row.avatar" fit="contain" />
            </div>
            <div class="information">
              <p class="line">
                <i class="el-icon-phone" />
                <span>手机：{{ scope.row.phone }}</span>
              </p>
              <p class="line">
                <i class="el-icon-message" />
                <span>邮箱：{{ scope.row.email }}</span>
              </p>
            </div>
          </div>
        </template>
      </el-table-column>
      <el-table-column :sortable="true" label="最后登陆" align="center" width="150">
        <template slot-scope="scope">
          <i class="el-icon-time" />
          {{ scope.row.time | formatTime }}
        </template>
      </el-table-column>
      <el-table-column label="状态" align="center" width="100">
        <template slot-scope="scope">
          <el-tag v-show="!scope.row.status" type="info">已停用</el-tag>
          <el-tag v-show="scope.row.status" type="success">使用中</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="操作" align="center" width="230">
        <template slot-scope="scope">
          <app-link :id="scope.row.id" :to="edit">
            <el-button type="default" size="mini" class="right-fix">编辑</el-button>
          </app-link>
          <el-button v-if="!scope.row.status" size="mini" type="success">启用</el-button>
          <el-button v-else size="mini" type="info">停用</el-button>
          <el-button size="mini" type="danger">删除</el-button>
        </template>
      </el-table-column>
    </el-table>
    <pagination
      v-show="total>0"
      :total="total"
      :page-sizes="pageSizes"
      :page.sync="listQuery.page"
      :limit.sync="listQuery.limit"
      @pagination="fetchList"
    />
  </div>
</template>
<script>
import { formatTime } from '@/utils/index'
import MenuContainer from '@/components/MenuContainer'
import AppLink from '@/components/AppLink'
import Pagination from '@/components/Pagination'

export default {
  name: 'MembersManagementIndex',
  components: {
    MenuContainer,
    AppLink,
    Pagination
  },
  filters: {
    formatTime(value) {
      return formatTime(value, '{y}-{m}-{d} {h}:{i}')
    }
  },
  data() {
    return {
      loading: true,
      edit: 'MembersManagementEdit',
      batch: {
        enable: '启用',
        disable: '停用',
        delete: '删除'
      },
      list: [
        {
          id: 1,
          avatar:
            'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif?imageView2/1/w/80/h/80',
          passport: 'zhangsan',
          phone: '13700000000',
          email: 'zhangsan@gmail.com',
          time: 1559290014,
          status: 1
        },
        {
          id: 2,
          passport: 'lisi',
          phone: '15900000000',
          email: 'lisi@gmail.com',
          time: 1559290014,
          status: 0
        }
      ],
      total: 60,
      pageSizes: [10, 20, 30, 50, 100],
      listQuery: {
        page: 1,
        limit: 20,
        keyword: ''
      }
    }
  },
  created() {
    this.fetchList()
  },
  methods: {
    fetchList() {
      this.loading = true
      setTimeout(() => {
        this.loading = false
      }, 300)
    }
  }
}
</script>
<style lang="scss" scoped>
.box {
  .avatar {
    float: left;
    margin-right: 10px;
    width: 60px;
    height: 60px;
  }
  .information {
    float: left;
    .line {
      margin: 5px 0;
    }
  }
}
</style>
