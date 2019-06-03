<template>
  <div class="app-container">
    <menu-container
      :app-link="link"
      :batch="batch"
    />
    <el-table
      ref="mainTable"
      v-loading="loading"
      :data="list"
      border
      stripe
      tooltip-effect="dark"
      @selection-change="handleSelectionChange"
    >
      <el-table-column type="selection" align="center" width="55" />
      <el-table-column align="center" label="排序" width="80">
        <template slot-scope="scope">
          <el-input
            ref="sequence"
            v-model="scope.row.sequence"
            size="mini"
            style="text-align:center"
            @focus="inputFocus"
          />
        </template>
      </el-table-column>
      <el-table-column label="角色" align="center" width="120">
        <template slot-scope="scope">{{ scope.row.roles? scope.row.roles.name : '-' }}</template>
      </el-table-column>
      <el-table-column label="账号信息" min-width="200">
        <template slot-scope="scope">
          <p>{{ scope.row.nickname }} <b class="color-theme">({{ scope.row.name }})</b></p>
          <p v-if="scope.row.email" class="email">{{ scope.row.email }}</p>
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
          <el-button type="default" size="mini" @click="goEditor(scope.row.id)">编辑</el-button>
          <el-button
            v-if="!scope.row.status"
            size="mini"
            type="success"
          >启用</el-button>
          <el-button
            v-if="scope.row.status"
            size="mini"
            type="info"
          >停用</el-button>
          <el-button size="mini" type="danger">删除</el-button>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>
<script>
import { formatTime } from '@/utils/index'
import MenuContainer from '@/components/MenuContainer'

export default {
  name: 'UserList',
  components: {
    MenuContainer
  },
  filters: {
    formatTime(value) {
      return formatTime(value, '{y}-{m}-{d} {h}:{i}')
    }
  },
  data() {
    return {
      loading: true,
      link: {
        url: '/users/management/insert',
        name: '新成员'
      },
      batch: {
        sequence: '排序',
        enable: '启用',
        disable: '停用',
        delete: '删除'
      },
      list: [
        {
          id: 1,
          roles: {
            name: '管理员'
          },
          name: 'admin',
          nickname: '张三',
          email: 'admin@gmail.com',
          time: 1559290014,
          status: 1,
          sequence: 0
        },
        {
          id: 2,
          roles: {
            name: '小编'
          },
          name: 'editor',
          nickname: '李四',
          email: 'editor@gmail.com',
          time: 1559290014,
          status: 0,
          sequence: 0
        }
      ],
      multipleSelection: []
    }
  },
  created() {
    this.fetchList()
  },
  methods: {
    goEditor(id) {
      this.$router.push({
        path: '/users/management/edit/' + id
      })
    },
    fetchList() {
      this.loading = true
      setTimeout(() => {
        this.loading = false
      }, 300)
    },
    inputFocus(e) {
      if (e.currentTarget) {
        e.currentTarget.select()
      }
    },
    handleSelectionChange(val) {
      this.multipleSelection = val
    }
  }
}
</script>
<style lang="scss" scoped>
p{
  margin: 0;
  &.email{
    color: #a4a4a4;
  }
}
</style>
