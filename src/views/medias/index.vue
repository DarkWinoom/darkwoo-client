<template>
  <div class="app-container no-padding">
    <el-container>
      <el-aside class="left-side" width="200px">
        <el-menu default-active="0">
          <el-menu-item v-for="(item,key) in catalogs" :key="key" :index="'' + key">
            <i :class="item.icon" />
            <span slot="title">{{ item.name }}</span>
          </el-menu-item>
        </el-menu>
        <div class="button">
          <el-button size="medium">批量删除</el-button>
        </div>
      </el-aside>
      <el-main>
        <div class="breadcrumb">
          <div class="item root">
            <span>根目录</span>
            <i class="el-icon-arrow-right" />
          </div>
          <div v-for="item in floder" :key="item.id" class="item">
            <span>{{ item.name }}</span>
            <i class="el-icon-arrow-right" />
          </div>
        </div>
        <el-table
          ref="table"
          v-loading="loading"
          :data="list"
          :row-style="{cursor: 'pointer'}"
          highlight-current-row
          class="adaptive-files"
          tooltip-effect="dark"
          @row-click="handleRowClick"
        >
          <el-table-column :selectable="setSelectable" type="selection" align="center" width="55" />
          <el-table-column label="文件" min-width="120">
            <template slot-scope="scope">
              <i v-show="!isFolder(scope.row)" :class="scope.row.mime | getFileIcon" />
              <span :class="{'folder': isFolder(scope.row)}">{{ scope.row.name }}</span>
            </template>
          </el-table-column>
          <el-table-column :sortable="true" prop="time" label="修改时间" width="180">
            <template slot-scope="scope">
              <i v-show="scope.row.time" class="el-icon-time" />
              <span class="time">{{ scope.row.time | formatTime }}</span>
            </template>
          </el-table-column>
          <el-table-column :sortable="true" prop="size" label="大小" width="120" align="right">
            <template slot-scope="scope">{{ scope.row.size || '' }}</template>
          </el-table-column>
        </el-table>
      </el-main>
      <el-aside class="right-side" width="200px">
        <preview :preview="preview" />
      </el-aside>
    </el-container>
  </div>
</template>
<script>
import { formatTime } from '@/utils'
import Preview from './components/Preview'

export default {
  name: 'MediasIndex',
  components: {
    Preview
  },
  filters: {
    formatTime(value) {
      return value ? formatTime(value, '{y}/{m}/{d} {h}:{i}') : ''
    },
    getFileIcon(mime) {
      if (mime.indexOf('image') !== -1) {
        return 'el-icon-picture-outline'
      } else if (mime.indexOf('video') !== -1) {
        return 'el-icon-mobile-phone'
      } else if (mime.indexOf('audio') !== -1) {
        return 'el-icon-service'
      } else {
        return 'el-icon-document'
      }
    }
  },
  data() {
    return {
      loading: true,
      floder: [
        {
          id: 1,
          name: '2019'
        },
        {
          id: 2,
          name: '0412'
        }
      ],
      catalogs: [
        {
          name: '所有文件',
          icon: 'el-icon-menu',
          value: undefined
        },
        {
          name: '图片',
          icon: 'el-icon-picture-outline',
          value: 'image'
        },
        {
          name: '视频',
          icon: 'el-icon-mobile-phone',
          value: 'video'
        },
        {
          name: '音频',
          icon: 'el-icon-service',
          value: 'audio'
        },
        {
          name: '其他文档',
          icon: 'el-icon-document',
          value: 'others'
        }
      ],
      list: [
        {
          name: '2019',
          mime: 'directory',
          size: 0,
          time: '1557299401'
        },
        {
          name: 'test.pdf',
          mime: 'application/pdf',
          size: '7.83 MB',
          time: '1555038809'
        },
        {
          name: 'test.gif',
          mime: 'image/gif',
          size: '75.3 KB',
          time: '1557299401',
          path:
            'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif'
        }
      ],
      preview: undefined,
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
    isFolder(row) {
      return row.mime === 'directory'
    },
    handleRowClick(row) {
      // 查看文件详情
      this.$refs.table.setCurrentRow(row)
      this.preview = row
    },
    setSelectable(row, index) {
      return !this.isFolder(row)
    }
  }
}
</script>
<style lang="scss" scoped>
.el-container {
  .el-aside {
    min-height: calc(100vh - 50px);
    .el-menu {
      border: none;
    }
    .button {
      padding: 20px;
      border-top: 1px solid #ebeef5;
    }
    &.left-side {
      border-right: 1px solid #ebeef5;
    }
    &.right-side {
      border-left: 1px solid #ebeef5;
    }
  }
  .el-main {
    padding: 0;
    .breadcrumb {
      padding: 15px;
      border-bottom: 1px solid #ebeef5;
      .item {
        display: inline-block;
        span {
          font-size: 14px;
          color: #95a5a6;
          cursor: pointer;
        }
        i {
          font-size: 13px;
          margin: 0 6px;
          font-weight: 400;
          color: #95a5a6;
        }
        &.root {
          span {
            font-weight: 600;
            color: #405261;
          }
        }
        &:last-child {
          span {
            cursor: default;
          }
          i {
            display: none;
          }
        }
      }
    }
    .folder {
      font-weight: bold;
      user-select: none;
    }
    .time {
      margin-left: 3px;
    }
  }
}
</style>
