<template>
  <div class="app-container">
    <menu-container :batch="batch">
      <template slot="left-menu">
        <el-input
          v-model="listQuery.keyword"
          clearable
          class="orders-search"
          size="small"
          placeholder="请输入内容关键字"
        >
          <el-select
            slot="prepend"
            v-model="listQuery.passport"
            clearable
            size="small"
            placeholder="账号过滤"
          >
            <el-option value="admin">zhangsan</el-option>
            <el-option value="editor">lisi</el-option>
          </el-select>
          <el-button slot="append" icon="el-icon-search" size="small">搜索</el-button>
        </el-input>
      </template>
    </menu-container>
    <el-table ref="mainTable" v-loading="loading" :data="list" border stripe tooltip-effect="dark">
      <el-table-column type="selection" align="center" width="55" />
      <el-table-column label="用户名" align="center" width="120">
        <template slot-scope="scope">{{ scope.row.passport }}</template>
      </el-table-column>
      <el-table-column label="问题内容" min-width="200">
        <template slot-scope="scope">{{ scope.row.content }}</template>
      </el-table-column>
      <el-table-column :sortable="true" label="提交时间" align="center" width="150">
        <template slot-scope="scope">
          <i class="el-icon-time" />
          {{ scope.row.time | formatTime }}
        </template>
      </el-table-column>
      <el-table-column label="状态" align="center" width="100">
        <template slot-scope="scope">
          <el-tag v-show="!scope.row.status" type="info">已结束</el-tag>
          <el-tag v-show="scope.row.status" type="success">解决中</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="操作" align="center" width="160">
        <template slot-scope="scope">
          <app-link :id="scope.row.id" :to="edit">
            <el-button type="default" size="mini" class="right-fix">编辑</el-button>
          </app-link>
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
  name: 'MembersOrdersIndex',
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
      edit: 'MembersOrdersEdit',
      batch: {
        delete: '删除'
      },
      list: [
        {
          id: 1,
          passport: 'zhangsan',
          content: '第一个问题的描述信息',
          email: 'zhangsan@gmail.com',
          time: 1559290014,
          status: 1
        },
        {
          id: 2,
          passport: 'lisi',
          content: '第二个问题的描述信息',
          time: 1559290014,
          status: 0
        }
      ],
      total: 60,
      pageSizes: [10, 20, 30, 50, 100],
      listQuery: {
        page: 1,
        limit: 20,
        passport: '',
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
<style lang="scss">
.orders-search{
    .el-select .el-input {
    width: 130px;
  }
  .el-input-group__prepend {
    background-color: #fff;
  }
}
</style>

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
