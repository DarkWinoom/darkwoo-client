<template>
  <div class="app-container no-padding">
    <submit-bar :back-url="back" @submit="submit" />
    <div class="form-container">
      <el-form
        ref="form"
        v-loading="loading"
        :model="form"
        :rules="rules"
        status-icon
        label-width="80px"
      >
        <el-tabs v-model="activeName" type="border-card">
          <el-tab-pane label="基本信息" name="base">
            <el-form-item label="名称" prop="name">
              <el-input v-model="form.name" />
            </el-form-item>
            <el-form-item label="描述" prop="description">
              <el-input v-model="form.description" :autosize="{ minRows: 4}" type="textarea" />
            </el-form-item>
            <el-form-item label="序号" prop="sequence">
              <el-input v-model.number="form.sequence" />
            </el-form-item>
          </el-tab-pane>
          <el-tab-pane label="系统权限" name="system">
            <purview-table
              :data="roles.system"
              :form="form.roles.system"
              :expand-all="true"
              class="system-table"
              @change="handleSystemChange"
            />
          </el-tab-pane>
          <el-tab-pane label="栏目权限" name="column">
            <purview-table
              :data="roles.column"
              :form="form.roles.column"
              :expand-all="true"
              class="column-table"
              @change="handleColumnChange"
            />
          </el-tab-pane>
        </el-tabs>
      </el-form>
    </div>
  </div>
</template>
<script>
import SubmitBar from '@/components/SubmitBar'
import purviewTable from './components/purviewTable'

export default {
  components: { SubmitBar, purviewTable },
  data() {
    return {
      loading: true,
      id: undefined,
      back: '/users/roles/list',
      activeName: 'base',
      roles: {
        system: [
          {
            id: 1,
            name: '控制台',
            disable: [2, 4, 8]
          },
          {
            id: 2,
            name: '网站栏目管理'
          },
          {
            id: 100,
            name: '会员中心',
            list: [
              { id: 101, name: '会员管理' },
              { id: 102, name: '积分管理' },
              { id: 103, name: '客服工单' }
            ]
          }
        ],
        column: [
          {
            id: 1,
            name: '关于我们',
            list: [
              { id: 11, name: '公司介绍' },
              { id: 12, name: '发展历程' }
            ]
          },
          {
            id: 2,
            name: '新闻中心',
            list: [
              { id: 21, name: '企业新闻' },
              { id: 22, name: '行业新闻' }
            ]
          },
          {
            id: 3,
            name: '联系我们'
          }
        ]
      },
      form: {
        id: undefined,
        name: '超级管理员',
        description: '',
        sequence: 0,
        roles: {
          system: {
            1: ['1'],
            2: ['8', '4', '2', '1'],
            3: ['2', '1']
          },
          column: {
            1: ['1', '4'],
            11: ['8', '4', '2', '1'],
            22: ['4', '2', '1']
          }
        }
      },
      rules: {
        name: [
          { required: true, message: '请输入职责名称', trigger: 'blur' },
          { min: 2, max: 20, message: '名称在2-20字符之间', trigger: 'blur' }
        ]
      }
    }
  },
  computed: {
    isMobile() {
      return this.$store.state.app.device === 'mobile'
    },
    isEdit() {
      return !!this.id
    }
  },
  mounted() {
    // console.log(this._data)
  },
  created() {
    this.id = this.$route.params && this.$route.params.id
    if (!this.isEdit) {
      // 获取添加资料
      setTimeout(() => {
        this.loading = false
      }, 300)
    } else {
      // 获取修改资料
      setTimeout(() => {
        this.loading = false
      }, 300)
    }
  },
  methods: {
    handleSystemChange(value) {
      // 这里返回的内容会存在空值，可以考虑在此过滤（或者提交的时候过滤）
      this.form.roles.system = value
    },
    handleColumnChange(value) {
      // 这里返回的内容会存在空值，可以考虑在此过滤（或者提交的时候过滤）
      this.form.roles.column = value
    },
    submit() {
      this.$refs['form'].validate(valid => {
        if (valid) {
          this.$confirm('确定提交吗?', '系统提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'success'
          }).then(() => {
            console.log(this.form)
            this.$message({
              type: 'success',
              message: '提交成功!'
            })
            this.$router.push({
              path: this.back
            })
          })
        } else {
          this.$message.error('请根据错误提示进行修正')
        }
      })
    }
  }
}
</script>
<style lang="scss" scoped>
.form-container {
  padding: 20px;
  .purview-box {
    .line {
      position: relative;
      .checkbox {
        position: absolute;
        top: 0;
        right: 0;
      }
    }
  }
}
</style>

