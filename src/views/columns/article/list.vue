<template>
  <div class="app-container">
    <menu-container :app-link="link" :batch="batch">
      <template slot="left-menu">
        <el-input
          v-model="listQuery.keyword"
          clearable
          class="article-search"
          size="small"
          placeholder="请输入关键字"
        >
          <el-select
            slot="prepend"
            v-model="listQuery.passport"
            clearable
            size="small"
            placeholder="栏目过滤"
          >
            <el-option
              v-for="item in map.columns"
              :key="item.id"
              :label="item.title"
              :value="item.id"
            />
          </el-select>
          <el-button slot="append" icon="el-icon-search" size="small">搜索</el-button>
        </el-input>
      </template>
      <template slot="right-menu">
        <el-button type="primary" icon="el-icon-download" size="small">导出Excel</el-button>
      </template>
    </menu-container>
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
      <el-table-column v-for="row in filterList" :key="row.key" :prop="row.key" :label="row.label" :sortable="row.sortable? 'custom':false" :min-width="row.minWidth" :width="row.width" :align="row.align">
        <template slot-scope="scope">
          <i v-show="row.icon" :class="row.icon" />
          <span>{{ scope.row[row.key] }}</span>
          <el-tag v-show="row.tagShow">{{ formatMap(scope.row[row.tagShow],row.tagShow) }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column :sortable="true" label="发布时间" align="center" width="150">
        <template slot-scope="scope">
          <i class="el-icon-time" />
          {{ scope.row.time | formatTime }}
        </template>
      </el-table-column>
      <el-table-column label="状态" align="center" width="100">
        <template slot-scope="scope">
          <el-tag v-show="!scope.row.status" type="info">已隐藏</el-tag>
          <el-tag v-show="scope.row.status" type="success">发布中</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="操作" align="center" width="230">
        <template slot-scope="scope">
          <el-button type="default" size="mini">编辑</el-button>
          <el-button v-if="!scope.row.status" size="mini" type="success">发布</el-button>
          <el-button v-else size="mini" type="info">隐藏</el-button>
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
import Pagination from '@/components/Pagination'

export default {
  name: 'ColumnsArticleList',
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
      link: {
        route: 'ColumnsArticleEdit',
        name: '新文章'
      },
      batch: {
        sequence: '排序',
        enable: '启用',
        disable: '禁用',
        delete: '删除'
      },
      map: {
        columns: [
          {
            id: '1',
            title: '栏目一'
          },
          {
            id: '2',
            title: '栏目二'
          }
        ]
      },
      filter: [
        {
          key: 'columns',
          label: '栏目',
          hidden: true
        },
        {
          key: 'title',
          label: '标题',
          tagShow: 'columns',
          minWidth: 200
        },
        {
          key: 'views',
          label: '浏览',
          width: 80,
          align: 'center',
          sortable: true
        },
        {
          key: 'stars',
          label: '点赞',
          width: 80,
          align: 'center',
          sortable: true
        }
      ],
      list: [
        {
          id: 1,
          columns: 1,
          title: '新闻标题1',
          views: '10',
          stars: '10',
          time: 1559290014,
          status: 1,
          sequence: 0
        },
        {
          id: 2,
          columns: 2,
          title: '新闻标题2',
          views: '20',
          stars: '12',
          time: 1559290014,
          status: 0,
          sequence: 0
        }
      ],
      total: 60,
      pageSizes: [10, 20, 30, 50, 100],
      listQuery: {
        page: 1,
        limit: 20,
        keyword: ''
      },
      multipleSelection: []
    }
  },
  computed: {
    filterList() {
      return this.filter.filter((item) => {
        return !item.hidden
      })
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
    formatMap(value, key) {
      if (this.map[key]) {
        const items = this.map[key].reduce((acc, cur) => {
          acc[cur.id] = cur.title
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
<style lang="scss">
.article-search {
  .el-select .el-input {
    width: 120px;
  }
  .el-input-group__prepend {
    background-color: #fff;
  }
}
</style>
