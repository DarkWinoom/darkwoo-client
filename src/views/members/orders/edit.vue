<template>
  <div class="app-container no-padding">
    <submit-bar :back-route="backRoute" />
    <div class="box">
      <el-card class="question" shadow="always">
        <p><span>问题标题：</span>第一个问题的描述信息</p>
        <p><span>提问会员：</span>张三</p>
        <p><span>提交时间：</span>2019-06-05 16:24</p>
        <p><span>当前状态：</span>解决中</p>
        <div class="reply">
          <div class="title">快速回复</div>
          <el-form ref="form" :model="form" label-width="80px">
            <el-form-item label="显示名称">
              <el-input v-model="form.name" />
            </el-form-item>
            <el-form-item label="回复内容">
              <el-input
                v-model="form.content"
                type="textarea"
                :autosize="{ minRows: 4, maxRows: 8}"
              />
            </el-form-item>
            <el-form-item label="状态设定">
              <el-radio-group v-model="form.status" size="medium">
                <el-radio border label="1">解决中</el-radio>
                <el-radio border label="0">已结束</el-radio>
              </el-radio-group>
            </el-form-item>
          </el-form>
        </div>
      </el-card>
      <div class="record">
        <div class="title">沟通记录</div>
        <el-timeline>
          <el-timeline-item timestamp="2019-06-05 16:31" placement="top">
            <el-card>
              <div class="thumbnails">
                <el-image :src="member.avatar" fit="cover" />
              </div>
              <div class="information">
                <span class="name">{{ member.name }}:</span>
                <span class="content">{{ '正文内容'.repeat(50) }}</span>
              </div>
            </el-card>
          </el-timeline-item>
          <el-timeline-item timestamp="2019-06-05 13:25" placement="top">
            <el-card class="admin">
              <div class="information">
                <span class="name">管理员:</span>
                <span class="content">{{ '正文内容'.repeat(50) }}</span>
              </div>
            </el-card>
          </el-timeline-item>
        </el-timeline>
      </div>
    </div>
  </div>
</template>
<script>
import SubmitBar from '@/components/SubmitBar'

export default {
  components: { SubmitBar },
  data() {
    return {
      backRoute: 'MembersOrderstIndex',
      member: {
        name: 'lisi',
        avatar:
          'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif?imageView2/1/w/80/h/80'
      },
      form: {
        name: '管理员',
        content: '',
        status: '1'
      }
    }
  }
}
</script>
<style lang="scss" scoped>
.box {
  padding: 20px;
  .title {
    font-size: 14px;
    color: #333333;
    text-indent: 16px;
    height: 40px;
    line-height: 38px;
    border: 1px solid #e1e6eb;
    border-left: 4px solid #6d7781;
    background: #f5f6fa;
  }
  .question {
    p {
      margin: 0;
      line-height: 1.7;
      span{
          color: #999;
      }
    }
    .reply {
      margin-top: 20px;
      .el-form {
        .el-form-item {
          margin: 20px 0 0;
          .el-radio{
              margin: 0 10px 0 0;
          }
        }
      }
    }
  }
  .record {
    margin-top: 20px;
    .el-timeline {
      font-size: 15px;
      line-height: 1.7;
      padding: 20px 0 0 20px;
      .el-card {
        .thumbnails {
          position: absolute;
          .el-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
          }
        }
        .information {
          padding-left: 60px;
        }
        &.admin {
          .information {
            padding-left: 0;
            font-weight: bold;
          }
        }
      }
    }
  }
}
</style>

