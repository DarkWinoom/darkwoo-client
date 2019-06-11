<template>
  <div class="app-container no-padding">
    <submit-bar @submit="submit" />
    <el-form
      ref="form"
      v-loading="loading"
      class="form-container"
      :model="form"
      :rules="rules"
      label-width="80px"
    >
      <el-tabs v-model="activeName" :tab-position="isMobile? 'top':'left'">
        <el-tab-pane label="基本信息" name="base">
          <el-row :gutter="20">
            <el-col :sm="16">
              <el-form-item label="标题" prop="title">
                <el-input v-model="form.title" />
              </el-form-item>
              <el-form-item label="副标题" prop="subtitle">
                <el-input v-model="form.subtitle" />
              </el-form-item>
              <el-form-item label="所在栏目" prop="columns">
                <el-select v-model="form.columns" filterable placeholder="请选择一个栏目">
                  <el-option label="栏目一" value="1" />
                  <el-option label="栏目二" value="2" />
                </el-select>
              </el-form-item>
              <el-form-item label="发布时间" required>
                <el-form-item prop="time">
                  <el-date-picker
                    v-model="form.time"
                    type="datetime"
                    format="yyyy-MM-dd HH:mm"
                    placeholder="选择日期"
                  />
                </el-form-item>
              </el-form-item>
              <el-form-item label="活动性质" prop="type">
                <el-checkbox-group v-model="form.type">
                  <el-checkbox label="美食/餐厅线上活动" name="type" />
                  <el-checkbox label="地推活动" name="type" />
                  <el-checkbox label="线下主题活动" name="type" />
                  <el-checkbox label="单纯品牌曝光" name="type" />
                </el-checkbox-group>
              </el-form-item>
              <el-form-item label="特殊资源" prop="resource">
                <el-radio-group v-model="form.resource">
                  <el-radio size="medium" label="线上品牌商赞助" />
                  <el-radio size="medium" label="线下场地免费" />
                </el-radio-group>
              </el-form-item>
              <el-form-item label="活动形式" prop="desc">
                <el-input v-model="form.desc" :autosize="{ minRows: 4, maxRows: 8 }" type="textarea" />
              </el-form-item>
            </el-col>
            <el-col :sm="8">
              <Thumbnail />
            </el-col>
          </el-row>
        </el-tab-pane>
        <el-tab-pane label="详细内容" name="details">详细内容</el-tab-pane>
        <el-tab-pane label="展示图片" name="photos">展示图片</el-tab-pane>
        <el-tab-pane label="附件管理" name="files">附件管理</el-tab-pane>
        <el-tab-pane label="评论管理" name="comment">评论管理</el-tab-pane>
        <el-tab-pane label="页面描述" name="descript">
          <DescriptForm />
        </el-tab-pane>
      </el-tabs>
    </el-form>
  </div>
</template>
<script>
import SubmitBar from '@/components/SubmitBar'
import DescriptForm from '../article/components/UniversalForm/Descript'
import Thumbnail from '@/components/Thumbnail'

export default {
  name: 'ColumnsArticleEdit',
  components: { SubmitBar, DescriptForm, Thumbnail },
  data() {
    return {
      loading: true,
      activeName: 'base',
      form: {
        title: '',
        subtitle: '',
        columns: '',
        time: new Date(Date.now()),
        type: [],
        resource: '',
        desc: ''
      },
      rules: {
        title: [{
          required: true,
          message: '请输入标题',
          trigger: 'blur'
        },
        {
          min: 1,
          max: 100,
          message: '标题在100个字符以内',
          trigger: 'blur'
        }
        ],
        subtitle: [{
          min: 1,
          max: 100,
          message: '副标题在100个字符以内',
          trigger: 'blur'
        }],
        columns: [{
          required: true,
          message: '请选择所在栏目',
          trigger: 'change'
        }],
        time: [{
          type: 'date',
          required: true,
          message: '请选择时间',
          trigger: 'change'
        }],
        type: [{
          type: 'array',
          required: true,
          message: '请至少选择一个活动性质',
          trigger: 'change'
        }],
        resource: [{
          required: true,
          message: '请选择活动资源',
          trigger: 'change'
        }],
        desc: [{
          required: true,
          message: '请填写活动形式',
          trigger: 'blur'
        }]
      }
    }
  },
  computed: {
    isMobile() {
      return this.$store.state.app.device === 'mobile'
    }
  },
  created() {
    setTimeout(() => {
      this.loading = false
    }, 300)
  },
  methods: {
    submit() {
      this.$refs['form'].validate(valid => {
        if (valid) {
          alert('submit!')
        } else {
          console.log('error submit!!')
          return false
        }
      })
    }
  }
}
</script>
<style lang="scss" scoped>
.form-container {
  padding: 20px;
}
</style>
