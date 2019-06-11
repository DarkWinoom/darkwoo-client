<template>
  <div class="app-container no-padding">
    <submit-bar :back-route="backRoute" @submit="submit" />
    <el-form
      ref="form"
      v-loading="loading"
      :model="form"
      :rules="rules"
      class="form-container"
      status-icon
      label-width="80px"
    >
      <el-row :gutter="20">
        <el-col :sm="16">
          <el-form-item label="账号" prop="passport">
            <el-input v-model="form.passport" placeholder="用户的账号名" auto-complete="off" />
          </el-form-item>
          <el-form-item label="邮箱" prop="email">
            <el-input v-model="form.email" placeholder="可用于登录和账号找回" />
          </el-form-item>
          <el-form-item label="密码" prop="password">
            <el-input
              v-model="form.password"
              placeholder="留空不会修改原密码"
              auto-complete="off"
              show-password
            />
          </el-form-item>
          <el-form-item label="手机" prop="phone">
            <el-input v-model="form.phone" placeholder="用户的手机号码" />
          </el-form-item>
          <el-form-item label="状态">
            <el-radio-group v-model="form.status" size="medium">
              <el-radio border label="1">启用</el-radio>
              <el-radio border label="0">停用</el-radio>
            </el-radio-group>
          </el-form-item>
        </el-col>
        <el-col :sm="8">
          <thumbnail v-model="avatar" title="用户头像" />
        </el-col>
      </el-row>
    </el-form>
  </div>
</template>
<script>
import SubmitBar from '@/components/SubmitBar'
import Thumbnail from '@/components/Thumbnail'

export default {
  components: { SubmitBar, Thumbnail },
  data() {
    return {
      backRoute: 'MembersManagementIndex',
      loading: true,
      id: undefined,
      avatar:
        'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif',
      form: {
        passport: '',
        password: '',
        avatar: 0,
        email: '',
        phone: '',
        status: '1'
      },
      rules: {
        passport: [
          { required: true, message: '请输入登录账号', trigger: 'blur' },
          { min: 2, max: 20, message: '账号在2-20字符之间', trigger: 'blur' }
        ],
        email: [
          { type: 'email', message: '请输入正确的邮箱地址', trigger: 'blur' }
        ],
        password: [
          { min: 5, max: 20, message: '密码在5-20字符之间', trigger: 'blur' }
        ],
        phone: [
          {
            validator: (rule, value, callback) => {
              if (value) {
                const phone_reg = /^[1][3,4,5,7,8][0-9]{9}$/
                if (!phone_reg.test(value)) {
                  callback(new Error('请输入正确的手机号码'))
                } else {
                  callback()
                }
              } else {
                callback()
              }
            },
            trigger: 'blur'
          }
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
    setTimeout(() => {
      this.loading = false
    }, 300)
  },
  methods: {
    submit() {
      this.$refs['form'].validate(valid => {
        if (valid) {
          if (!this.form.avatar) {
            this.$message.error('请选择一张图片作为用户头像')
          } else {
            this.$confirm('确定提交吗?', '系统提示', {
              confirmButtonText: '确定',
              cancelButtonText: '取消',
              type: 'success'
            }).then(() => {
              this.$message({
                type: 'success',
                message: '提交成功!'
              })
              this.$router.push({
                path: this.back
              })
            })
          }
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
  .el-radio {
    margin-right: 0;
  }
}
</style>

