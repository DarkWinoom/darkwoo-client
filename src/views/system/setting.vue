<template>
  <div class="app-container no-padding">
    <submit-bar @submit="submit" />
    <el-form ref="form" v-model="form" class="form-container" label-width="80px">
      <el-tabs v-model="activeName" type="border-card">
        <el-tab-pane name="site">
          <span slot="label">
            <i class="el-icon-setting" /> 站点设置
          </span>
          <el-form-item label="站点标题">
            <el-input v-model="form.title" />
          </el-form-item>
          <el-form-item label="关键字">
            <el-input v-model="form.title" />
          </el-form-item>
          <el-form-item label="描述">
            <el-input v-model="form.title" :autosize="{ minRows: 2, maxRows: 5}" type="textarea" />
          </el-form-item>
          <el-form-item label="备案号">
            <el-input v-model="form.title" />
          </el-form-item>
          <el-form-item label="版权信息">
            <el-input v-model="form.title" />
          </el-form-item>
        </el-tab-pane>
        <el-tab-pane name="interaction">
          <span slot="label">
            <i class="el-icon-refresh" /> 交互设置
          </span>
          <el-form-item label="会员注册">
            <el-select v-model="form.title">
              <el-option label="开放注册" value="shanghai" />
              <el-option label="需要审核" value="shanghai" />
              <el-option label="禁止注册" value="shanghai" />
            </el-select>
          </el-form-item>
        </el-tab-pane>
        <el-tab-pane name="background">
          <span slot="label">
            <i class="el-icon-date" /> 后台设置
          </span>
          占位符
        </el-tab-pane>
        <el-tab-pane name="maintenance">
          <span slot="label">
            <i class="el-icon-share" /> 维护模式
          </span>
          <el-alert :closable="false" title="启用维护模式后，所有站点页面将无法访问，直至关闭该模式（不影响后台使用）" type="warning" />
          <div class="content">
            <el-checkbox v-model="form.maintenanceOn">启用维护模式</el-checkbox>
            <div class="tips">当启用时, 访问者会看到下述内容</div>
            <el-input
              v-model="form.maintenanceText"
              :autosize="{ minRows: 5, maxRows: 10}"
              type="textarea"
              placeholder="系统维护中"
            />
          </div>
        </el-tab-pane>
      </el-tabs>
    </el-form>
  </div>
</template>
<script>
import SubmitBar from '@/components/SubmitBar'
export default {
  name: 'SystemSetting',
  components: { SubmitBar },
  data() {
    return {
      activeName: 'maintenance',
      form: {
        title: '',
        maintenanceOn: '',
        maintenanceText: '系统维护中'
      }
    }
  },
  methods: {
    submit() {
      this.$confirm('确定提交吗?', '系统提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'success'
      }).then(() => {
        this.$message({
          type: 'success',
          message: '提交成功!'
        })
      })
    }
  }
}
</script>
<style lang="scss" scoped>
.form-container {
  padding: 20px;
}
.content {
  padding-top: 15px;
  .el-checkbox {
    font-size: 15px !important;
  }
  .tips {
    padding: 5px 0 0 23px;
    font-size: 14px;
    color: #909399;
  }
  .el-textarea {
    padding: 15px 0 0 20px;
  }
}
</style>

