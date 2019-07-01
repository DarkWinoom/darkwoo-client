<template>
  <div class="el-upload__tip">
    <template v-if="showSwitch">
      <el-checkbox v-model="checked" @change="handleChange">强制上传</el-checkbox>
      <el-popover
        placement="right"
        title="强制上传"
        width="300"
        trigger="hover"
        content="开启后将不会使用快传和断点续传功能，对正在上传中的文件无效"
      >
        <i slot="reference" class="el-icon-warning" />
      </el-popover>
    </template>
    <template v-if="queueLimit > 0">
      <p v-if="queueLimit === 1">请选择单一文件</p>
      <p v-else>数量限制：{{ queueLimit }}</p>
    </template>
    <p>格式限制：{{ typeLimitTips }}</p>
    <p v-show="cropShow">尺寸建议：{{ cropWidth }} x {{ cropHeight }}</p>
    <p>单文件大小限制：{{ sizeLimitTips }}</p>
  </div>
</template>

<script>
import { formatSize } from '@/utils'

export default {
  name: 'UploaderUploadTips',
  props: {
    showSwitch: {
      type: Boolean,
      default: true
    },
    queueLimit: {
      type: Number,
      default: 0
    },
    sizeLimit: {
      type: Number,
      default: 0
    },
    typeLimit: {
      type: Array,
      default() {
        return []
      }
    },
    cropShow: {
      type: Boolean,
      default: true
    },
    cropWidth: {
      type: Number,
      default: 120
    },
    cropHeight: {
      type: Number,
      default: 120
    }
  },
  data() {
    return {
      checked: false
    }
  },
  computed: {
    typeLimitTips() {
      // 格式转换成文字提示
      if (this.typeLimit.length > 0) {
        const typeTips = []
        for (const type of this.typeLimit) {
          let suffix
          switch (type) {
            case 'image':
              suffix = '图片'
              break
            case 'video':
              suffix = '视频'
              break
            case 'audio':
              suffix = '音频'
              break
            default:
              suffix = type
          }
          typeTips.push(suffix)
        }
        return typeTips.join(', ') + '格式'
      } else {
        return '不限'
      }
    },
    sizeLimitTips() {
      // 大小转换成文字提示
      if (this.sizeLimit) {
        return formatSize(this.sizeLimit)
      } else {
        return '不限'
      }
    }
  },
  methods: {
    handleChange() {
      this.$emit('change', this.checked)
    }
  }
}
</script>

<style lang="scss" scoped>
.el-upload__tip {
  font-size: 14px;
  color: #909399;
  line-height: 28px;
  margin-top: 10px;
  p{
    margin: 0;
  }
}
.el-checkbox{
    margin: 0;
}
</style>
