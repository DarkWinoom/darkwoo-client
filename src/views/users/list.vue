<template>
  <div class="app-container">
    <div class="filter-container">
      <app-link :to="insertLink">
        <el-button class="filter-item" size="small" type="success" icon="el-icon-plus">新成员</el-button>
      </app-link>
      <batch-operation />
      <el-table
        ref="multipleTable"
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
            <el-tag :type="scope.row.status? 'success':'info'">
              {{ filterRow(scope.row.status,'status') }}
            </el-tag>
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
  </div>
</template>
<script>
import { formatTime } from '@/utils/index'
import AppLink from '@/layout/components/Sidebar/Link'
import BatchOperation from '@/components/BatchOperation'

export default {
  name: 'UserList',
  components: {
    AppLink,
    BatchOperation
  },
  filters: {
    formatTime(value) {
      return formatTime(value, '{y}-{m}-{d} {h}:{i}')
    }
  },
  data() {
    return {
      loading: true,
      insertLink: '/users/management/insert',
      filter: {
        status: [
          {
            id: 1,
            name: '使用中'
          },
          {
            id: 0,
            name: '已停用'
          }
        ]
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
          id: 1,
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
    filterRow(value, key) {
      if (this.filter[key]) {
        const items = this.filter[key].reduce((acc, cur) => {
          acc[cur.id] = cur.name
          return acc
        }, {})
        if (items[value]) {
          return items[value]
        } else {
          return '-'
        }
      } else {
        return value
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
.filter-container {
    padding-bottom: 0;

    .filter-item {
        display: inline-block;
        vertical-align: middle;
        margin: 0 5px 20px 0;
    }

    &.bottom {
        padding: 20px 0 0;
    }
}
</style>
