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
          <app-link :id="scope.row.id" :to="edit">
            <el-button type="default" size="mini" class="right-fix">编辑</el-button>
          </app-link>
          <el-button size="mini" type="danger">删除</el-button>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>
<script>
import MenuContainer from '@/components/MenuContainer'
import AppLink from '@/components/AppLink'

export default {
  name: 'UserRoleList',
  components: {
    MenuContainer,
    AppLink
  },
  data() {
    return {
      loading: true,
      edit: 'EditUserRole',
      link: {
        route: 'NewUserRole',
        name: '新角色'
      },
      batch: {
        sequence: '排序',
        delete: '删除'
      },
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
      multipleSelection: []
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
