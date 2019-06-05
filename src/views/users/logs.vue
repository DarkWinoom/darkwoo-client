<template>
  <div class="app-container">
    <menu-container>
      <template slot="left-menu">
        <el-select
          v-model="listQuery.passport"
          clearable
          class="item"
          size="small"
          placeholder="账号过滤"
        >
          <el-option value="admin">admin</el-option>
          <el-option value="editor">editor</el-option>
        </el-select>
      </template>
      <template slot="right-menu">
        <el-button size="small" type="primary" icon="el-icon-delete">清空记录</el-button>
      </template>
    </menu-container>
    <el-table
      ref="multipleTable"
      v-loading="loading"
      :data="simList"
      border
      stripe
      tooltip-effect="dark"
    >
      <el-table-column label="账号" align="center" width="200">
        <template slot-scope="scope">{{ scope.row.passport }}</template>
      </el-table-column>
      <el-table-column label="详情">
        <template slot-scope="scope">{{ scope.row.remark }}</template>
      </el-table-column>
      <el-table-column :sortable="true" label="时间" align="center" width="200">
        <template slot-scope="scope">
          <i class="el-icon-time" />
          {{ scope.row.time | formatTime }}
        </template>
      </el-table-column>
      <el-table-column :sortable="true" label="状态" align="center" width="150">
        <template slot-scope="scope">
          <el-tag v-show="!scope.row.status" type="danger">登录失败</el-tag>
          <el-tag v-show="scope.row.status" type="success">登录成功</el-tag>
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
import Pagination from '@/components/Pagination'
export default {
  name: 'UsersLogs',
  components: {
    MenuContainer,
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
      list: [
        {
          id: 1,
          passport: 'admin',
          remark: '',
          time: 1559290014,
          status: 1
        },
        {
          id: 2,
          passport: 'admin',
          remark: '',
          time: 1559290014,
          status: 0
        }
      ],
      total: 60,
      pageSizes: [10, 20, 30, 50, 100],
      listQuery: {
        page: 1,
        limit: 20,
        passport: ''
      }
    }
  },
  computed: {
    simList() {
      const list = [...this.list]
      for (let i = 2; i < 20; i++) {
        list.push({
          id: this.round(5),
          passport: 'admin' + this.round(2),
          remark: '',
          time: this.round(10),
          status: this.round(1) > 4 ? 1 : 0
        })
      }
      return list
    }
  },
  created() {
    this.fetchList()
  },
  methods: {
    fetchList(listQuery = {}) {
      console.log(listQuery)
      this.loading = true
      setTimeout(() => {
        this.loading = false
      }, 300)
    },
    round(n) {
      var rnd = ''
      for (var i = 0; i < n; i++) {
        rnd += Math.floor(Math.random() * 10)
      }
      return rnd
    }
  }
}
</script>
<style lang="scss" scoped>
.item {
  margin-right: 10px;
}
</style>

