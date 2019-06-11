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
            <el-input v-model="form.passport" placeholder="登录的账号名" auto-complete="off" />
          </el-form-item>
          <el-form-item label="邮箱" prop="email">
            <el-input v-model="form.email" placeholder="可用于登录和账号找回，请真实填写" />
          </el-form-item>
          <el-form-item label="密码" prop="password">
            <el-col :span="12" style="padding-left:0">
              <el-input
                v-model="form.password"
                :placeholder="isEdit? '留空不会修改原密码':'登录密码'"
                auto-complete="off"
                show-password
              />
            </el-col>
            <el-col :span="12" style="padding-right:0">
              <el-input v-model="form.confirm" placeholder="再次输入密码" show-password />
            </el-col>
          </el-form-item>
          <el-form-item label="角色" prop="roles_id">
            <el-select v-model="form.roles_id" placeholder="请选择一个职责角色">
              <el-option v-for="item in roles" :key="item.id" :label="item.name" :value="item.id" />
            </el-select>
            <el-button v-if="form.roles_id">权限预览</el-button>
          </el-form-item>
          <el-form-item label="昵称" prop="nickname">
            <el-input v-model="form.nickname" placeholder="显示的用户名称" />
          </el-form-item>
          <el-form-item label="手机" prop="phone">
            <el-input v-model="form.phone" placeholder="常用的手机号码" />
          </el-form-item>
          <el-form-item label="序号" prop="sequence">
            <el-input v-model.number="form.sequence" auto-complete="off" />
          </el-form-item>
          <el-form-item label="状态">
            <el-radio-group v-model="form.status" size="medium">
              <el-radio border label="1">启用</el-radio>
              <el-radio border label="0">停用</el-radio>
            </el-radio-group>
          </el-form-item>
        </el-col>
        <el-col :sm="8">
          <thumbnail v-model="avatar" :required="true" title="用户头像" />
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
      backRoute: 'UsersManagementIndex',
      loading: true,
      roles: [],
      id: undefined,
      avatar:
        'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif',
      form: {
        roles_id: '',
        passport: '',
        password: '',
        confirm: '',
        avatar: 0,
        nickname: '',
        email: '',
        phone: '',
        status: '1',
        sequence: 0
      },
      rules: {
        passport: [
          { required: true, message: '请输入登录账号', trigger: 'blur' },
          { min: 2, max: 20, message: '账号在2-20字符之间', trigger: 'blur' }
        ],
        email: [
          { required: true, message: '请输入邮箱地址', trigger: 'blur' },
          { type: 'email', message: '请输入正确的邮箱地址', trigger: 'blur' }
        ],
        password: [
          {
            validator: (rule, value, callback) => {
              if (value) {
                if (value.length < 5 || value.length > 20) {
                  callback(new Error('密码在5-20字符之间'))
                } else if (!this.form.confirm) {
                  callback(new Error('请输入确认密码'))
                } else if (value !== this.form.confirm) {
                  callback(new Error('两次输入密码不一致'))
                } else {
                  callback()
                }
              } else {
                callback()
              }
            },
            trigger: 'blur'
          }
        ],
        roles_id: [
          { required: true, message: '请选择一个权限职责', trigger: 'blur' }
        ],
        nickname: [
          { required: true, message: '请输入用户昵称', trigger: 'blur' }
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
        ],
        sequence: [
          {
            validator: (rule, value, callback) => {
              if (!Number.isInteger(value)) {
                callback(new Error('序号必须是整数'))
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
    if (!this.isEdit) {
      // 获取添加资料
      setTimeout(() => {
        this.loading = false
      }, 300)
      this.rules.password.push({
        required: true,
        message: '请输入密码',
        trigger: 'blur'
      })
    } else {
      // 获取修改资料
      setTimeout(() => {
        this.loading = false
      }, 300)
    }
  },
  methods: {
    handleChangeAvatar(avatar_id) {
      this.form.avatar = avatar_id
    },
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

