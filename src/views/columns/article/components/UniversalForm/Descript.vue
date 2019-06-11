<template>
  <div>
    <el-alert
      :closable="false"
      class="alert-tips"
      title="该页面为SEO相关设置，一般情况下无需修改"
      type="warning"
      show-icon
    />
    <el-row :gutter="20">
      <el-col :sm="14">
        <el-form-item label="标题">
          <el-input placeholder="关于_XXXX公司网站" />
        </el-form-item>
        <el-form-item label="描述">
          <el-input :autosize="{ minRows: 4 }" type="textarea" placeholder="XXXX公司是一家专门......" />
        </el-form-item>
        <el-form-item label="关键字">
          <el-select
            v-model="keywords"
            multiple
            filterable
            allow-create
            default-first-option
            placeholder="请输入或选择关键字"
            style="margin-right:10px"
          >
            <el-option key="1" label="地产" value="地产" />
            <el-option key="2" label="广告" value="广告" />
          </el-select>
          <div class="form-tips inline">当前默认：地产, 广告, 促销</div>
        </el-form-item>
      </el-col>
      <el-col :sm="10">
        <el-card class="box-card" shadow="always">
          <div slot="header" class="header">
            <span>不追踪页面</span>
          </div>
          <div class="text item">
            <el-switch
              v-model="pageFollow"
              active-color="#F56C6C"
              inactive-color="#67C23A"
              active-text="开启"
              inactive-text="关闭"
            />
            <div v-if="pageFollow" class="tips danger text-left">
              <p>选择开启“不追踪页面”</p>
              <p>搜索引擎将不会收录当前页面，也不会继续追踪页面中的链接地址</p>
            </div>
            <div v-else class="tips text-left">
              <p>选择关闭“不追踪页面”</p>
              <p>搜索引擎将会正常收录页面与页面中的链接地址</p>
            </div>
          </div>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>
<script>
export default {
  name: 'UniversalFormDescript',
  data() {
    return {
      keywords: [],
      pageFollow: 0
    }
  }
}
</script>
<style lang="scss" scoped>
.alert-tips {
  margin-bottom: 20px;
}
.tips {
  color: #67C23A;
  font-weight: bold;
  margin-top: 15px;
  p {
    line-height: 1.9;
    font-size: 14px;
    margin: 0;
  }
  &.danger{
    color: #F56C6C;
  }
}
.form-tips {
  font-size: 14px;
  color: #909399;
  margin-top: 10px;
  line-height: 1.5;

  &.inline {
    display: inline-block;
  }
}
</style>

