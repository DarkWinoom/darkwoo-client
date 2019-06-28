<template>
  <el-card class="box-card" shadow="always">
    <div slot="header" class="header">
      <span :class="{required:required}">{{ title }}</span>
      <div class="editor">
        <el-button
          type="text"
          icon="el-icon-edit-outline"
          @click="showModal"
        >{{ image? '更换':'新增' }}</el-button>
        <el-button v-if="image" type="text" icon="el-icon-close" @click="handleRemoveImage">移除</el-button>
      </div>
    </div>
    <div class="text item">
      <div v-if="image" class="thumbnails">
        <el-image :src="image" alt @click="showModal" />
      </div>
      <uploader-modal
        v-model="uploadModalCount"
        target="http://localhost/laboratory/uploader.php"
        :size-limit="500 * 1024 * 1024"
        :type-limit="['image', 'video', 'rar']"
        :queue-limit="0"
        :crop-width="400"
        :crop-height="300"
        :crop-fixed="[4,3]"
        @complete="complete"
      />
      <div class="tips">
        <p>推荐尺寸：{{ width }}像素 × {{ height }}像素</p>
        <p class="dpi">（{{ dpi }}像素分辨率）</p>
        <p class="remind">请选择相同或等比例尺寸的图片文件</p>
      </div>
    </div>
  </el-card>
</template>
<script>
import UploaderModal from '@/components/Uploader/Modal'

export default {
  name: 'Thumbnail',
  components: {
    UploaderModal
  },
  props: {
    value: {
      // 默认显示的头像
      type: String,
      default: ''
    },
    title: {
      // 显示的标题
      type: String,
      default: '缩略图'
    },
    width: {
      // 推荐宽度
      type: Number,
      default: 120
    },
    height: {
      // 推荐高度
      type: Number,
      default: 120
    },
    dpi: {
      // 像素分辨率推荐
      type: Number,
      default: 96
    },
    required: {
      // 是否必须选择图片
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      uploadModalCount: 0,
      image: this.value
    }
  },
  watch: {
    value(value) {
      this.image = this.value
    }
  },
  methods: {
    showModal() {
      this.uploadModalCount++
    },
    complete(message) {
      this.$message.success('上传成功')
      console.log(message)
    },
    handleRemoveImage() {
      this.$confirm(
        '此操作将取消当前' + this.title + ', 是否继续?',
        '系统提示',
        {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }
      ).then(() => {
        this.image = ''
      })
    }
  }
}
</script>
<style lang="scss" scoped>
.box-card {
  .header {
    position: relative;

    .required {
      &:before {
        content: "*";
        color: #f56c6c;
        margin-right: 4px;
      }
    }

    .editor {
      position: absolute;
      top: 50%;
      right: 0;
      transform: translateY(-50%);
    }
  }

  .item {
    position: relative;
    text-align: center;
    .thumbnails {
      padding-bottom: 15px;

      img {
        display: inline-block;
        max-width: 100%;
        cursor: pointer;
      }
    }
    .tips {
      font-size: 14px;
      p {
        margin: 0;
        line-height: 1.9;
        &.dpi {
          color: #909399;
        }
        &.remind {
          color: #e6a23c;
        }
      }
    }
  }
}
</style>
