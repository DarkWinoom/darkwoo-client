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
            @click="handleCreate(0)"
          >新增</el-button>
          <div class="input">
            <el-input v-model="filter" size="small" :clearable="true" placeholder="搜索">
              <i slot="prefix" class="el-input__icon el-icon-search" />
            </el-input>
          </div>
          <el-button
            class="sorting"
            size="small"
            @click="showSorting = !showSorting"
          >{{ showSorting? '排序模式':'列表模式' }}</el-button>
        </div>
        <el-tree
          ref="tree"
          v-loading.fullscreen="loading"
          :data="treeList"
          :props="{ label: 'title', children: 'children' }"
          :highlight-current="true"
          :default-expand-all="true"
          :expand-on-click-node="false"
          :filter-node-method="handleFilterNode"
          element-loading-text="请稍候"
          node-key="id"
          @node-click="handleEdit"
        >
          <span slot-scope="{ node, data }" class="custom-tree-node">
            <span>
              <el-input
                v-show="showSorting"
                ref="sorting"
                v-model="data.sorting"
                class="sorting"
                size="mini"
                title="回车键修改"
                @focus="inputFocus"
                @keyup.enter.native="handleSorting(data,data.sorting)"
              />
              <el-tag v-show="data.status === 0" size="mini" type="info">已隐藏</el-tag>
              <template v-if="data.id === form.id && form.changed">
                <i class="tips">*</i>
              </template>
              {{ data.title }}
            </span>
            <span>
              <template v-if="showSorting">
                <el-button
                  type="text"
                  size="mini"
                  @click="handleSorting(data,data.sorting)"
                >
                  排序确认
                </el-button>
              </template>
              <template v-else>
                <el-button
                  type="text"
                  size="mini"
                  @click.stop.native="handleCreate(data.id)"
                >
                  新增
                </el-button>
                <el-button
                  type="text"
                  size="mini"
                  @click.stop.native="handleDelete(data)"
                >
                  删除
                </el-button>
              </template>
            </span>
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
                @click="handleCreate(0)"
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
                    :ignore="form.id"
                  />
                </el-select>
              </el-form-item>
              <el-form-item label="序号" prop="sorting">
                <el-input v-model="form.sorting" @input="handleFormChange" />
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
              @click="handleDelete(form)"
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
      sorting: 0,
      status: 1,

      show: false, // 是否显示
      changed: false // 表示是否修改过，修改过时切换页面予以提示
    }
    return {
      activeName: 'base', // 当前激活的标签页
      loading: false, // loading
      showSorting: false, // 是否在树中显示排序框
      filter: '', // 标题检索
      form: form, // 待提交表单
      initForm: form, // 初始化信息的表单
      rules: {
        title: [{ required: true, message: '请输入标题', trigger: 'blur' }]
      },
      treeList: [],
      list: [
        // 数据库列表
        {
          id: 1,
          fid: 0,
          title: '关于我们',
          sorting: 0,
          status: 1
        },
        {
          id: 3,
          fid: 0,
          title: '新闻中心',
          sorting: 0,
          status: 1
        },
        {
          id: 2,
          fid: 0,
          title: '联系我们',
          sorting: 0,
          status: 1
        },
        {
          id: 4,
          fid: 1,
          title: '公司介绍',
          sorting: 0,
          status: 1
        },
        {
          id: 6,
          fid: 1,
          title: '办公地点',
          sorting: 0,
          status: 1
        },
        {
          id: 11,
          fid: 5,
          title: '2018年',
          sorting: 0,
          status: 1
        },
        {
          id: 10,
          fid: 5,
          title: '2017年',
          sorting: 0,
          status: 1
        },
        {
          id: 5,
          fid: 1,
          title: '发展历程',
          sorting: 0,
          status: 1
        },
        {
          id: 7,
          fid: 3,
          title: '企业新闻',
          sorting: 0,
          status: 1
        },
        {
          id: 8,
          fid: 3,
          title: '行业新闻',
          sorting: 0,
          status: 1
        },
        {
          id: 20,
          fid: 0,
          title: '已隐藏',
          sorting: 0,
          status: 0
        }
      ]
    }
  },
  watch: {
    filter(value) {
      this.$refs.tree.filter(value)
    },
    list: {
      handler: function() {
        this.treeList = this.getTreeList()
      },
      deep: true
    }
  },
  created() {
    this.$nextTick(() => {
      this.treeList = this.getTreeList()
    })
  },
  methods: {
    inputFocus(e) {
      if (e.currentTarget) {
        e.currentTarget.select()
      }
    },
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
        return a.sorting - b.sorting
      })
      remaining.sort(function(a, b) {
        return a.sorting - b.sorting
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
            if (item.children) {
              recursive(item.children)
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
      return data.title.indexOf(value) !== -1
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
    handleSorting(data, sorting) {
      // 排序
      this.loading = true
      setTimeout(() => {
        this.loading = false
        const index = this.list.findIndex(item => item.id === data.id)
        if (this.list[index].sorting !== sorting) {
          this.list[index].sorting = sorting
        } else {
          // 排序数字没有变化，不需要提交
          console.log('no sorting change')
        }
      }, 300)
    },
    handleCreate(fid = 0) {
      // 获取对应栏目的添加数据
      if (this.$refs.tree.getCurrentKey()) {
        setTimeout(() => {
          this.$refs.tree.setCurrentKey(null)
        }, 10)
      }
      this.formChangeTips(() => {
        this.form = {
          ...this.initForm,
          fid: fid
        }
        this.form.show = true
      })
    },
    handleEdit(data, node) {
      // 获取对应栏目的修改数据
      if (node && this.showSorting) return false
      if (data.id === this.form.id) return false
      const form = {
        id: data.id,
        fid: data.fid,
        title: data.title,
        sorting: data.sorting,
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
      this.$refs.editorForm.validate(valid => {
        if (valid) {
          const form = {
            id: this.form.id,
            fid: this.form.fid,
            title: this.form.title,
            sorting: this.form.sorting,
            status: this.form.status
          }
          if (form.id === 0) {
            // 添加
            this.$confirm('确认添加吗?', '系统提示', {
              confirmButtonText: '确定',
              cancelButtonText: '取消',
              type: 'warning'
            }).then(() => {
              this.loading = true
              setTimeout(() => {
                this.loading = false
                form.id = Math.ceil(Math.random() * 10000)
                this.list.push(form)
                this.close(true)
              }, 300)
            })
          } else {
            // 修改
            this.$confirm('确认修改吗?', '系统提示', {
              confirmButtonText: '确定',
              cancelButtonText: '取消',
              type: 'warning'
            }).then(() => {
              this.loading = true
              setTimeout(() => {
                this.loading = false
                const index = this.list.findIndex(item => item.id === form.id)
                this.list[index] = form
                this.treeList = this.getTreeList()
                this.form.changed = false
                setTimeout(() => {
                  this.$refs.tree.setCurrentKey(form.id)
                }, 10)
              }, 300)
            })
          }
        } else {
          console.log('error submit!!')
          return false
        }
      })
    },
    handleDelete(data) {
      // 删除
      this.$confirm('确认删除“' + data.title + '”吗?', '系统提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        this.loading = true
        setTimeout(() => {
          this.list.splice(this.list.findIndex(item => item.id === data.id), 1)
          this.loading = false
          this.close(true)
        }, 300)
      })
    },
    close(force = false) {
      // 关闭表单页面
      if (force === true) {
        this.form.changed = false
      }
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
  .left-board {
    .el-tree {
      .el-tree-node__content {
        height: inherit;
        .sorting {
          text-align: center;
          width: 43px;
          padding: 5px 5px 5px 0;
          input {
            padding: 0 6px;
          }
        }
      }
    }
    .button input {
      border-radius: 0;
    }
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
