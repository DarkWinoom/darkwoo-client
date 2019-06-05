<template>
  <div class="app-container">
    <el-row :gutter="20" class="columns-tree">
      <el-col :span="8" class="left-board">
        <div class="button">
          <el-button icon="el-icon-plus">新增</el-button>
          <div class="input">
            <el-input v-model="filter" :clearable="true" placeholder="搜索">
              <i slot="prefix" class="el-input__icon el-icon-search" />
            </el-input>
          </div>
        </div>
        <el-tree
          ref="tree"
          v-loading.fullscreen="loading"
          :data="treeList"
          :props="defaultProps"
          :highlight-current="true"
          :default-expand-all="true"
          :expand-on-click-node="false"
          :filter-node-method="filterNode"
          element-loading-text="请稍候"
          node-key="id"
          class="tree"
          @node-click="handleNodeClick"
        >
          <span slot-scope="{ node, data }" class="custom-tree-node">
            <span>{{ (data.id === form.id && form.changed)? '* ':'' }}{{ node.label }}</span>
            <el-tag v-show="data.status === 0" size="mini" type="info">已隐藏</el-tag>
          </span>
        </el-tree>
      </el-col>
      <el-col :span="16" class="right-board">
        <div v-show="!form.show" class="empty">
          <p>
            <svg-icon icon-class="tree" style="font-size:36px" />
          </p>
          <p>点击左侧“新增”按钮或列表中的栏目名称，可以打开栏目编辑窗口</p>
        </div>
        <el-form v-show="form.show" ref="editorForm" :model="form" label-width="80px">
          <el-tabs v-show="JSON.stringify(form) !== '{}'" v-model="activeName">
            <el-tab-pane label="基本信息" name="base">
              <el-form-item label="排序" class="base-order">
                <el-button-group>
                  <el-button type="primary" size="mini" icon="el-icon-caret-top">上移</el-button>
                  <el-button type="primary" size="mini" icon="el-icon-caret-bottom">下移</el-button>
                </el-button-group>
              </el-form-item>
              <el-form-item label="名称">
                <el-input v-model="form.title" @input="handleFormChange" />
              </el-form-item>
              <el-form-item label="所属">
                <el-select v-model="form.fid" placeholder="请选择一个栏目" @change="handleFormChange">
                  <el-option :value="0" label="一级栏目" />
                  <recursive-option v-model="form.fid" :list="treeList" />
                </el-select>
              </el-form-item>
              <el-form-item label="类型">
                <el-select v-model="form.pattern" placeholder="请选择栏目类型" @change="handleFormChange">
                  <el-option value="default" label="单页" />
                  <el-option value="list" label="列表" />
                </el-select>
              </el-form-item>
              <el-form-item label="状态" class="base-status">
                <el-radio-group v-model="form.status" @change="handleFormChange">
                  <el-radio :label="1">显示</el-radio>
                  <el-radio :label="0">隐藏</el-radio>
                </el-radio-group>
              </el-form-item>
            </el-tab-pane>
            <el-tab-pane label="页面描述" name="descript">
              <descript-form />
            </el-tab-pane>
          </el-tabs>
          <el-form-item class="base-button">
            <el-button type="primary" size="medium" icon="el-icon-check" @click="submit">提交</el-button>
            <el-button size="medium" icon="el-icon-close" @click="close">关闭</el-button>
            <el-button
              v-show="form.id"
              type="danger"
              size="medium"
              icon="el-icon-delete"
              class="delete"
            >删除</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import RecursiveOption from './components/RecursiveOption'
import DescriptForm from './components/UniversalForm/Descript'

export default {
  name: 'ColumnsManagementIndex',
  components: {
    RecursiveOption,
    DescriptForm
  },
  data() {
    return {
      activeName: 'base',
      loading: false,
      filter: '',
      form: {
        id: 0,
        fid: 0,
        title: '',
        status: 1,

        show: false, // 是否显示
        changed: false // 表示是否修改过，修改过时切换页面予以提示
      },
      tempShow: false,
      list: [
        {
          id: 1,
          fid: 0,
          label: '关于我们',
          pattern: 'default',
          sequence: 1,
          status: 1
        },
        {
          id: 3,
          fid: 0,
          label: '新闻中心',
          pattern: 'list',
          sequence: 2,
          status: 1
        },
        {
          id: 2,
          fid: 0,
          label: '联系我们',
          pattern: 'default',
          sequence: 3,
          status: 1
        },
        {
          id: 4,
          fid: 1,
          label: '公司介绍',
          pattern: 'default',
          sequence: 4,
          status: 1
        },
        {
          id: 6,
          fid: 1,
          label: '办公地点',
          pattern: 'default',
          sequence: 5,
          status: 1
        },
        {
          id: 11,
          fid: 5,
          label: '2018年',
          pattern: 'list',
          sequence: 6,
          status: 1
        },
        {
          id: 10,
          fid: 5,
          label: '2017年',
          pattern: 'list',
          sequence: 7,
          status: 1
        },
        {
          id: 5,
          fid: 1,
          label: '发展历程',
          pattern: 'list',
          sequence: 8,
          status: 1
        },
        {
          id: 7,
          fid: 3,
          label: '企业新闻',
          pattern: 'list',
          sequence: 9,
          status: 1
        },
        {
          id: 8,
          fid: 3,
          label: '行业新闻',
          pattern: 'list',
          sequence: 10,
          status: 1
        },
        {
          id: 20,
          fid: 0,
          label: '已隐藏',
          pattern: 'default',
          sequence: 11,
          status: 0
        }
      ],
      defaultProps: {
        label: 'label',
        children: 'children'
      },
      cascaderProps: {
        value: 'id',
        label: 'label',
        children: 'children'
      }
    }
  },
  computed: {
    treeList() {
      // 根据 list 自动生成树形结构
      var list = []
      var remaining = [] // 剩余未分配的栏目
      this.list.map(item => {
        if (parseInt(item.fid) === 0) {
          list.push({ ...item })
        } else {
          remaining.push({ ...item })
        }
      })
      // 对列表进行排序
      list.sort(function(a, b) {
        return a.sequence - b.sequence
      })
      remaining.sort(function(a, b) {
        return a.sequence - b.sequence
      })
      // 递归，支持无限层级
      const recursive = row => {
        if (remaining.length > 0) {
          for (const item of row) {
            for (const child of remaining) {
              if (child.fid === item.id) {
                if (item.children) {
                  item.children.push(child)
                } else {
                  item.children = [child]
                }
                remaining = remaining.filter(v => v.id !== child.id)
              }
            }
            if (item.children) recursive(item.children)
          }
        }
      }
      recursive(list)
      return list
    }
  },
  watch: {
    filter(value) {
      this.$refs.tree.filter(value)
    }
  },
  created() {
    this.$nextTick(() => {
      // this.$refs.tree.setCurrentKey(11)
      setTimeout(() => {
        // this.list[3].fid = 3
      }, 1000)
    })
  },
  methods: {
    filterNode(value, data) {
      if (!value) return true
      return data.label.indexOf(value) !== -1
    },
    handleFormChange() {
      this.form.changed = true
    },
    handleNodeClick(data) {
      const form = {
        id: data.id,
        fid: data.fid,
        title: data.label,
        pattern: data.pattern,
        sequence: data.sequence,
        status: data.status,

        show: true,
        changed: false
      }
      if (this.form.changed === true) {
        this.$confirm('未保存的内容将会丢失, 是否继续?', '系统提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.form = form
          this.activeName = 'base'
        })
      } else {
        this.form = form
        this.activeName = 'base'
      }
    },
    submit() {
      console.log(this.form)
    },
    close() {
      const reset = () => {
        this.form = {
          id: 0,
          fid: 0,
          title: '',
          status: 1,

          show: false,
          changed: false
        }
        setTimeout(() => {
          this.$refs.tree.setCurrentKey(null)
        }, 10)
      }
      if (this.form.changed === true) {
        this.$confirm('未保存的内容将会丢失, 是否继续?', '系统提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          reset()
        })
      } else {
        reset()
      }
    },
    resetForm() {
      this.$refs.formForm.resetFields()
    }
  }
}
</script>

<style lang="scss">
.columns-tree {
  .left-board .button input {
    border-radius: 0;
  }
}
</style>

<style lang="scss" scoped>
.el-row.columns-tree {
  border: 1px solid #ebeef5;
  .left-board {
    padding: 0 !important;
    border-right: 1px solid #ebeef5;
    .button {
      position: relative;
      margin: -1px -1px 0 -1px;
      .el-button {
        position: absolute;
        border-radius: 0;
      }
      .input {
        padding-left: 88px;
      }
    }
  }
  .right-board {
    position: relative;
    border-left: 1px solid #ebeef5;
    margin-left: -1px;
    .empty {
      margin: 100px 0 0;
      text-align: center;
    }
    .delete {
      float: right;
    }
  }
}
.custom-tree-node {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;
}
</style>
