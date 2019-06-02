<template>
  <div class="app-container">
    <div class="filter-container">
      <app-link :to="insertLink">
        <el-button class="filter-item" size="small" type="success" icon="el-icon-plus">新角色</el-button>
      </app-link>
      <batch-operation />
    </div>
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
      <el-table-column label="名称" width="200">
        <template slot-scope="scope">{{ scope.row.name }}</template>
      </el-table-column>
      <el-table-column label="描述" min-width="200">
        <template slot-scope="scope">{{ scope.row.description || '-' }}</template>
      </el-table-column>
      <el-table-column label="成员人数" align="center" width="100">
        <template slot-scope="scope">{{ scope.row.users_count }}</template>
      </el-table-column>
      <el-table-column label="操作" align="center" width="160">
        <template slot-scope="scope">
          <el-button type="default" size="mini" @click="goEditor(scope.row.id)">编辑</el-button>
          <el-button size="mini" type="danger">删除</el-button>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>
<script>
import AppLink from '@/layout/components/Sidebar/Link'
import BatchOperation from '@/components/BatchOperation'

export default {
  name: 'RoleList',
  components: {
    AppLink,
    BatchOperation
  },
  data() {
    return {
      insertLink: '/users/roles/insert',
      list: [
        {
          id: 1,
          name: '超级管理员',
          description: '描述内容',
          users_count: 1,
          sequence: 0
        },
        {
          id: 2,
          name: '小编',
          description: '',
          users_count: 0,
          sequence: 0
        }
      ],
      loading: true,
      multipleSelection: []
    }
  },
  created() {
    this.fetchList()
  },
  methods: {
    goEditor(id) {
      this.$router.push({
        path: '/users/roles/edit/' + id
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
.thumbnails{
  display: inline-block;
  margin-right: 5px;
}
</style>
