<template>
  <div>
    <div class="thumbnails">
      <template v-if="isImage(row.type)">
        <el-image :src="row.url" fit="contain">
          <div slot="error" class="image-slot">
            <i title="无法读取图片" class="el-icon-picture-outline" />
          </div>
        </el-image>
      </template>
      <template v-else>
        <i class="el-icon-document" />
      </template>
    </div>
    <div class="information">
      <p class="name">
        <span>{{ row.name }}</span>
      </p>
      <el-progress
        :text-inside="true"
        :stroke-width="15"
        :percentage="row | formatProgress"
        color="#67C23A"
      />
      <p class="size">
        <el-tag size="mini">{{ row.name | formatType }}</el-tag>
        <span>{{ row.uploadedSize | formatSize }}</span>
        /
        <span>{{ row.size | formatSize }}</span>
      </p>
    </div>
  </div>
</template>

<script>
import { formatSize } from '@/utils'

export default {
  filters: {
    formatSize(size) {
      return formatSize(size)
    },
    formatType(name) {
      return name.substr(name.lastIndexOf('.') + 1).toUpperCase()
    },
    formatProgress(row) {
      // 上传进度文本
      let progress = Math.floor(row.progress * 10000) / 100
      if (progress >= 100 && !row.isComplete) {
        progress = 99
      }
      return progress
    }
  },
  props: {
    data: {
      type: Object,
      required: true,
      default() {
        return {
          name: '',
          type: '',
          url: '',
          size: '',
          progress: '',
          isComplete: ''
        }
      }
    }
  },
  data() {
    return {
      row: this.data
    }
  },
  methods: {
    isImage(type) {
      return type.indexOf('image') !== -1
    }
  }
}
</script>

<style lang="scss" scoped>
.thumbnails {
  position: absolute;
    top: 12px;
  height: 30px;
  .el-image {
    position: relative;
    width: 30px;
    height: 30px;
  }
  i {
    position: relative;
    top: 3px;
    font-size: 30px;
  }
}
.information {
  padding-left: 40px;
  .el-progress {
    margin: 3px 0;
  }
  p {
    margin: 0;
    height: 23px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    &.name {
      font-size: 16px;
      color: #333;
    }
    &.size {
      font-size: 14px;
      color: #aaa;
    }
  }
}
</style>
