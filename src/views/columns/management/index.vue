<template>
  <div class="app-container">
    <el-row :gutter="20" class="columns-tree">
      <el-col :span="8" class="left-board">
        <div class="button">
          <el-button
            class="insert"
            size="small"
            type="success"
            plain
            icon="el-icon-plus"
            @click="handleCreate"
          >新增</el-button>
          <div class="input">
            <el-input v-model="filter" size="small" :clearable="true" placeholder="搜索">
              <i slot="prefix" class="el-input__icon el-icon-search" />
            </el-input>
          </div>
          <el-button class="sorting" size="small" icon="el-icon-sort">排序</el-button>
        </div>
        <el-tree
          ref="tree"
          v-loading.fullscreen="loading"
          draggable
          :data="treeList"
          :props="defaultProps"
          :highlight-current="true"
          :default-expand-all="true"
          :expand-on-click-node="false"
          :filter-node-method="handleFilterNode"
          element-loading-text="请稍候"
          node-key="id"
          class="tree"
          :allow-drag="handleAllowDrag"
          :allow-drop="handleAllowDrop"
          @node-drop="handleDrop"
          @node-click="handleEdit"
        >
          <span slot-scope="{ node, data }" class="custom-tree-node">
            <span>
              <template v-if="data.id === form.id && form.changed">
                <i class="tips">*</i>
              </template>
              {{ node[defaultProps.label] }}
            </span>
            <el-tag v-show="data.status === 0" size="mini" type="info">已隐藏</el-tag>
          </span>
        </el-tree>
      </el-col>
      <el-col :span="16" class="right-board">
        <div v-show="!form.show" class="empty">
          <p>
            <svg-icon icon-class="tree" style="font-size:36px" />
          </p>
          <div class="tips">
            <p>
              点击
              <el-button
                icon="el-icon-plus"
                size="mini"
                type="success"
                style="margin: 0 10px 0 5px"
                plain
                @click="handleCreate"
              >新增</el-button>按钮或列表中的栏目名称，可以打开栏目编辑窗口
            </p>
            <p>拖拽栏目可以改变排序，只能改变同级栏目</p>
          </div>
        </div>
        <el-form
          v-show="form.show"
          ref="editorForm"
          :rules="rules"
          :model="form"
          label-width="80px"
        >
          <el-tabs v-show="JSON.stringify(form) !== '{}'" v-model="activeName">
            <el-tab-pane label="基本信息" name="base">
              <el-form-item label="名称" prop="title">
                <el-input v-model="form.title" @input="handleFormChange" />
              </el-form-item>
              <el-form-item label="所属">
                <el-select v-model="form.fid" placeholder="请选择一个栏目" @change="handleFormChange">
                  <el-option :value="0" label="一级栏目" />
                  <recursive-option
                    v-model="form.fid"
                    :list="treeList"
                    :label-name="defaultProps.label"
                    :child-name="defaultProps.children"
                  />
                </el-select>
              </el-form-item>
              <el-form-item label="类型" prop="pattern">
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
import DescriptForm from '../article/components/UniversalForm/Descript'

export default {
  name: 'ColumnsManagementIndex',
  components: {
    RecursiveOption,
    DescriptForm
  },
  data() {
    const form = {
      id: 0,
      fid: 0,
      title: '',
      pattern: '',
      sequence: 0,
      status: 1,

      show: false, // 是否显示
      changed: false // 表示是否修改过，修改过时切换页面予以提示
    }
    return {
      activeName: 'base',
      loading: false,
      filter: '',
      form: form,
      initForm: form,
      rules: {
        title: [{ required: true, message: '请输入标题', trigger: 'blur' }],
        pattern: [{ required: true, message: '请选择类型', trigger: 'blur' }]
      },
      treeList: [],
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
        // 别名设定
        label: 'label',
        children: 'children'
      }
    }
  },
  watch: {
    filter(value) {
      this.$refs.tree.filter(value)
    },
    list: {
      handler: function() {
        this.treeList = this.getTreeList()
        console.log(this.treeList)
      },
      deep: true
    }
  },
  created() {
    this.$nextTick(() => {
      // this.$refs.tree.setCurrentKey(11)
      this.treeList = this.getTreeList()
      setTimeout(() => {
        // this.list[3].fid = 3
      }, 1000)
    })
  },
  methods: {
    getTreeList() {
      // 根据 list 自动生成树形结构
      var list = []
      var remaining = [] // 剩余未分配的栏目
      this.list.map(item => {
        if (parseInt(item.fid) === 0) {
          list.push({
            ...item
          })
        } else {
          remaining.push({
            ...item
          })
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
                if (item[this.defaultProps.children]) {
                  item[this.defaultProps.children].push(child)
                } else {
                  item[this.defaultProps.children] = [child]
                }
                remaining = remaining.filter(v => v.id !== child.id)
              }
            }
            if (item[this.defaultProps.children]) {
              recursive(item[this.defaultProps.children])
            }
          }
        }
      }
      recursive(list)
      return list
    },
    handleFilterNode(value, data) {
      // 搜索过滤
      if (!value) return true
      return data[this.defaultProps.label].indexOf(value) !== -1
    },
    handleAllowDrag(node) {
      // 判断当前是否可以被拖拽
      return true
    },
    handleAllowDrop(draggingNode, dropNode, type) {
      // 判断目标节点（dropNode）是否可以被放置
      if (draggingNode.level === dropNode.level && type !== 'inner') {
        // 只有同级节点可以更改，无法改变层级
        if (draggingNode.data.fid === dropNode.data.fid) {
          // 节点必须在相同父节点之下
          return true
        }
      } else {
        return false
      }
    },
    handleDrop(draggingNode, dropNode, dropType, ev) {
      this.loading = true;
      [draggingNode.data.sequence, dropNode.data.sequence] = [
        dropNode.data.sequence,
        draggingNode.data.sequence
      ]
      setTimeout(() => {
        this.$message.success('操作成功')
        this.loading = false
      }, 200)
      const setting = {}
      setting[draggingNode.data.id] = draggingNode.data.sequence
      setting[dropNode.data.id] = dropNode.data.sequence
      console.log(setting)
    },
    handleFormChange() {
      // 标记表单为已修改状态
      this.form.changed = true
    },
    formChangeTips(callback = () => {}) {
      // 检查表单是否修改过，若修改过将给与提示
      if (this.form.changed === true) {
        this.$confirm('未保存的内容将会丢失, 是否继续?', '系统提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          callback()
          this.activeName = 'base'
          this.$refs.editorForm.clearValidate()
        })
      } else {
        callback()
        this.activeName = 'base'
        this.$refs.editorForm.clearValidate()
      }
    },
    handleCreate() {
      // 获取对应栏目的添加数据
      if (this.$refs.tree.getCurrentKey()) {
        setTimeout(() => {
          this.$refs.tree.setCurrentKey(null)
        }, 10)
      }
      this.formChangeTips(() => {
        this.form = {
          ...this.initForm
        }
        this.form.show = true
      })
    },
    handleEdit(data) {
      // 获取对应栏目的修改数据
      if (data.id === this.form.id) return false
      const form = {
        id: data.id,
        fid: data.fid,
        title: data[this.defaultProps.label],
        pattern: data.pattern,
        sequence: data.sequence,
        status: data.status,

        show: true,
        changed: false
      }
      this.formChangeTips(() => {
        this.form = form
      })
    },
    submit() {
      // 提交
      /* this.list.push({
        id: Math.ceil(Math.random() * 10000),
        fid: this.form.fid,
        label: this.form.title,
        pattern: this.form.pattern,
        sequence: this.form.sequence,
        status: this.form.status
      }) */
      this.$refs.editorForm.validate(valid => {
        if (valid) {
          const form = {
            id: this.form.id,
            fid: this.form.fid,
            label: this.form.title,
            pattern: this.form.pattern,
            sequence: this.form.sequence,
            status: this.form.status
          }
          if (form.id === 0) {
            // 添加
            this.$confirm('确认添加吗?', '系统提示', {
              confirmButtonText: '确定',
              cancelButtonText: '取消',
              type: 'warning'
            }).then(() => {
              console.log('add', form)
            })
          } else {
            // 修改
            this.$confirm('确认修改吗?', '系统提示', {
              confirmButtonText: '确定',
              cancelButtonText: '取消',
              type: 'warning'
            }).then(() => {
              console.log('edit', form)
            })
          }
        } else {
          console.log('error submit!!')
          return false
        }
      })
    },
    close() {
      // 关闭表单页面
      this.formChangeTips(() => {
        this.form = {
          ...this.initForm
        }
        if (this.$refs.tree.getCurrentKey()) {
          setTimeout(() => {
            this.$refs.tree.setCurrentKey(null)
          }, 10)
        }
      })
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
</style><style lang="scss" scoped>
.el-row.columns-tree {
  border: 1px solid #ebeef5;

  .left-board {
    position: relative;
    z-index: 2;
    padding: 0 !important;
    border-right: 1px solid #ebeef5;

    .tips {
      font-style: normal;
      color: red;
    }

    .button {
      position: relative;
      padding-bottom: 1px;
      margin: -1px -1px 0 -1px;

      .el-button {
        position: absolute;
        padding-top: 8px;
        border-radius: 0;

        &.sorting {
          top: 0;
          right: 0;
        }
      }

      .input {
        padding: 0 72px;
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

    .tips {
      font-size: 16px;
      margin-top: 20px;
      line-height: 2;

      p {
        margin: 0;
      }
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
