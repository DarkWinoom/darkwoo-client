<template>
  <div class="app-container no-padding">
    <submit-bar @submit="submit" />
    <el-form ref="form" v-model="form" class="form-container" label-width="80px">
      <el-row :gutter="20">
        <el-col :span="16">
          <el-tabs v-model="activeNameLeft" type="border-card">
            <el-tab-pane name="site">
              <span slot="label">
                <i class="el-icon-setting" /> 站点设置
              </span>
              <el-form-item label="站点标题">
                <el-col :span="16">
                  <el-input v-model="form.title" />
                </el-col>
                <el-col :span="8">
                  <div class="line-tips">站点显示的名称。</div>
                </el-col>
              </el-form-item>
              <el-form-item label="关键字">
                <el-col :span="16">
                  <el-select
                    v-model="form.keyword"
                    style="width:100%"
                    multiple
                    filterable
                    allow-create
                    default-first-option
                    placeholder="请输入或选择关键字"
                  >
                    <el-option
                      v-for="item in keyword"
                      :key="item.value"
                      :label="item.label"
                      :value="item.value"
                    />
                  </el-select>
                </el-col>
                <el-col :span="8">
                  <div class="line-tips">站点的关键字，使用回车键可以输入多个。</div>
                </el-col>
              </el-form-item>
              <el-form-item label="描述">
                <el-col :span="16">
                  <el-input v-model="form.descript" :autosize="{ minRows: 4, maxRows: 8}" type="textarea" />
                </el-col>
                <el-col :span="8">
                  <div class="line-tips">站点的描述信息。</div>
                </el-col>
              </el-form-item>
              <el-form-item label="备案号">
                <el-col :span="16">
                  <el-input v-model="form.icp_record" />
                </el-col>
                <el-col :span="8">
                  <div class="line-tips">站点在工信部的备案号码。</div>
                </el-col>
              </el-form-item>
              <el-form-item label="版权信息">
                <el-col :span="16">
                  <el-input v-model="form.copyright" />
                </el-col>
                <el-col :span="8">
                  <div class="line-tips">站点的copyright信息。</div>
                </el-col>
              </el-form-item>
            </el-tab-pane>
            <el-tab-pane name="interaction">
              <span slot="label">
                <i class="el-icon-refresh" /> 交互设置
              </span>
              <el-form-item label="会员注册">
                <el-select v-model="form.registry">
                  <el-option label="开放注册" value="1" />
                  <el-option label="需要审核" value="0" />
                  <el-option label="禁止注册" value="-1" />
                </el-select>
              </el-form-item>
            </el-tab-pane>
            <el-tab-pane name="background">
              <span slot="label">
                <i class="el-icon-first-aid-kit" /> 安全设置
              </span>
              <el-form-item label="自动登出">
                <el-col :span="16">
                  <el-select v-model="form.timeout" style="width:100%">
                    <el-option value="0" label="不限制" />
                    <el-option value="10" label="10分钟" />
                    <el-option value="30" label="30分钟" />
                    <el-option value="60" label="1小时" />
                  </el-select>
                </el-col>
                <el-col :span="8">
                  <div class="line-tips">无任何操作时自动退出时间。</div>
                </el-col>
              </el-form-item>
              <el-form-item label="登录重试">
                <el-col :span="16">
                  <el-select v-model="form.retry" style="width:100%">
                    <el-option value="0" label="不限制" />
                    <el-option value="3" label="3次" />
                    <el-option value="5" label="5次" />
                    <el-option value="10" label="10次" />
                  </el-select>
                </el-col>
                <el-col :span="8">
                  <div class="line-tips">登录失败时允许重试次数。</div>
                </el-col>
              </el-form-item>
              <el-form-item label="重试间隔">
                <el-col :span="16">
                  <el-select v-model="form.interval" style="width:100%">
                    <el-option value="1" label="60秒" />
                    <el-option value="5" label="5分钟" />
                    <el-option value="10" label="10分钟" />
                    <el-option value="30" label="30分钟" />
                    <el-option value="60" label="1小时" />
                    <el-option value="120" label="2小时" />
                  </el-select>
                </el-col>
                <el-col :span="8">
                  <div class="line-tips">超过次数后再次登录间隔。</div>
                </el-col>
              </el-form-item>
            </el-tab-pane>
          </el-tabs>
        </el-col>
        <el-col :span="8">
          <el-tabs v-model="activeNameRight" type="border-card">
            <el-tab-pane name="maintenance">
              <span slot="label">
                <i class="el-icon-share" /> 维护模式
              </span>
              <el-alert :closable="false" title="启用后所有站点页面与资源将无法访问，直至关闭该模式（不影响后台使用）" type="warning" />
              <div class="content">
                <el-checkbox v-model="form.maintenanceOn">启用</el-checkbox>
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
        </el-col>
      </el-row>
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
      activeNameLeft: 'site',
      activeNameRight: 'maintenance',
      form: {
        title: 'XXXX网站',
        keyword: ['关键字1', '关键字2'],
        descript: '网站描述',
        icp_record: '京IPC备XXXXXXXX',
        copyright: '版权所有 © 2019',
        registry: '1',
        timeout: '10',
        retry: '5',
        interval: '60',
        maintenanceOn: '',
        maintenanceText: '系统维护中'
      },
      keyword: [
        {
          value: '关键字1',
          label: '关键字1'
        },
        {
          value: '关键字2',
          label: '关键字2'
        }
      ]
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
.line-tips{
  color: #909399;
}
</style>

