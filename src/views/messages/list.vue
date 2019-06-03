<template>
  <div class="app-container">
    <div class="board-title">
      <span class="name">收件箱</span>
      <span class="total">
        (共 {{ total }} 件，其中
        <span class="unread">{{ unread }}</span> 件未读)
      </span>
    </div>
    <div class="button">
      <el-button size="small">删除</el-button>
      <el-button size="small">标记为已读</el-button>
      <el-button size="small">标记为未读</el-button>
      <el-button size="small">全部标为已读</el-button>
    </div>
    <el-table
      ref="table"
      v-loading="loading"
      :data="list"
      :row-style="rowStyle"
      class="message-receive"
      @sort-change="handleSortChange"
      @selection-change="handleSelectionChange"
      @expand-change="handleRowExpand"
      @row-click="handleRowClick"
    >
      <el-table-column type="expand">
        <template slot-scope="props">
          <el-card class="content-expand" shadow="never">
            <div v-html="props.row.content" />
            <div v-if="props.row.attachments.length > 0" class="attachments">
              <div class="title">
                <i class="el-icon-edit" />
                附件 ({{ props.row.attachments.length }}个)
              </div>
              <div v-for="item in props.row.attachments" :key="item.id" class="line">
                <div v-if="item.isImage" class="thumbnails">
                  <img :src="item.link" alt>
                </div>
                <i class="el-icon-document" />
                <div class="information">
                  <span class="name">{{ item.name }}</span>
                  <span class="size">({{ item.size }})</span>
                </div>
                <div class="download">
                  <i class="el-icon-download" />
                  <span>点击下载</span>
                </div>
              </div>
            </div>
          </el-card>
        </template>
      </el-table-column>
      <el-table-column type="selection" align="center" width="55" />
      <el-table-column label="发件人" width="150" prop="user" />
      <el-table-column label="主题" min-width="200" prop="title">
        <template slot-scope="scope">
          <div>
            <el-tag v-show="!scope.row.hasRead" type="info" size="mini">未读</el-tag>
            <el-tag v-show="scope.row.attachments.length > 0" type="success" size="mini">有附件</el-tag>
            <span>{{ scope.row.title }}</span>
          </div>
          <div class="preview">{{ scope.row.preview }}</div>
        </template>
      </el-table-column>
      <el-table-column
        :sort-orders="['ascending', 'descending']"
        label="时间"
        sortable="custom"
        width="200"
        prop="time"
      >
        <template slot-scope="scope">
          <i class="el-icon-time" />
          <span>{{ scope.row.time | formatTime }}</span>
        </template>
      </el-table-column>
    </el-table>
    <pagination
      v-show="total>0"
      :bottom-fixed="true"
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
import Pagination from '@/components/Pagination'

export default {
  components: {
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
      total: 2,
      unread: 1,
      list: [
        {
          id: 1,
          title: '第一封信件',
          user: '张三',
          preview: '正文预览...',
          content: '正文内容',
          time: 135565588,
          hasRead: 1,
          attachments: [
            {
              id: 1,
              name: '档案文件.zip',
              link: '#',
              size: '1.24 MB'
            },
            {
              id: 2,
              isImage: 1,
              name: '图片.gif',
              link:
                'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif',
              size: '75.4 KB'
            }
          ]
        },
        {
          id: 2,
          title: '第二封信件',
          user: '李四',
          preview: '正文预览2...',
          content: '正文内容2',
          time: 123131435,
          hasRead: 0,
          attachments: []
        }
      ],
      pageSizes: [10, 20, 30, 50, 100],
      listQuery: {
        time: 'descending',
        page: 1,
        limit: 20
      },
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
    rowStyle(info) {
      // 行样式
      const style = { cursor: 'pointer' }
      if (!info.row.hasRead) {
        style.fontWeight = 'bold'
        style.color = '#99a9bf'
      }
      if (info.row.open) {
        style.background = '#f5f7fa'
      }
      return style
    },
    handleSortChange(handle) {
      // 点击排序
      if (handle.prop === 'time') {
        this.listQuery.time = handle.order
        this.fetchList()
      }
    },
    handleRowExpand(row) {
      // 展开行
      row.open = !row.open
      if (!row.hasRead) {
        this.unread--
      }
      row.hasRead = true
    },
    handleRowClick(row) {
      // 点击行任意位置
      this.$refs.table.toggleRowExpansion(row)
      this.handleRowExpand(row)
    },
    handleSelectionChange(val) {
      this.multipleSelection = val
    }
  }
}
</script>
<style lang="scss">
</style>

<style lang="scss">
.el-table.message-receive {
  .el-table__expanded-cell {
    background-color: #f5f7fa !important;
    &:hover {
      background-color: #f5f7fa !important;
    }
  }
}
</style>

<style lang="scss" scoped>
.board-title {
  font-size: 14px;
  margin: 0 0 5px 8px;
  padding-bottom: 5px;
  border-bottom: 1px solid #ebeef5;
  .name {
    font-weight: bold;
  }
  .total {
    .unread {
      color: #409eff;
      font-weight: bold;
    }
  }
}
.button {
  margin-bottom: 8px;
  padding-bottom: 5px;
}
.content-expand {
  line-height: 1.5;
  .attachments {
    margin-top: 20px;
    border: 1px solid #eee;
    .title {
      padding: 10px 15px;
      background: #eee;
    }
    .line {
      position: relative;
      padding: 10px 15px;
      .thumbnails {
        margin-bottom: 5px;
      }
      .information {
        display: inline-block;
        padding-right: 120px;
        .size {
          color: #aaa;
        }
      }
      .download {
        display: inline-block;
        position: absolute;
        bottom: 0;
        right: 0;
        padding: 10px 15px;
        cursor: pointer;
        &.no-exists {
          color: #f56c6c;
          cursor: default;
        }
      }
      + .line {
        border-top: 1px solid #eee;
      }
    }
  }
}
.preview {
  font-weight: normal !important;
  color: #a4a4a4 !important;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>
