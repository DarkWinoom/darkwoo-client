<template>
  <div v-show="preview.name" class="preview-container">
    <div v-show="thumbDisplay" class="thumbnails">
      <div class="box">
        <img v-show="mimeCheck('image')" :src="preview.path" alt>
        <video v-show="mimeCheck('video')" :src="preview.path" class="control" controls />
        <audio v-show="mimeCheck('audio')" :src="preview.path" class="control" controls />
      </div>
    </div>
    <div class="content">
      <div class="panel">
        <label>{{ isFolder? '文件夹':'文件名' }}</label>
        <div class="name">{{ preview.name }}</div>
        <table>
          <tbody>
            <tr v-show="preview.size">
              <th>大小</th>
              <td>{{ preview.size }}</td>
            </tr>
            <tr>
              <th>地址</th>
              <td v-show="!isFolder">
                <a :href="preview.path" target="_blank">点击打开</a>
              </td>
              <td v-show="isFolder">
                <a @click="handleClick">点击访问</a>
              </td>
            </tr>
            <tr>
              <th>修改</th>
              <td>{{ preview.time | formatTime }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { formatTime } from '@/utils'

export default {
  name: 'MediasPreview',
  filters: {
    formatTime(value) {
      return value ? formatTime(value, '{y}/{m}/{d} {h}:{i}') : '-'
    }
  },
  props: {
    preview: {
      // 预览的文件
      type: Object,
      default() {
        return {
          name: '',
          mime: '',
          size: '',
          time: '',
          path: ''
        }
      }
    }
  },
  data() {
    return {
      thumbDisplay: true,
      support: ['image', 'video', 'audio']
    }
  },
  computed: {
    isFolder() {
      return this.preview.mime === 'directory'
    }
  },
  watch: {
    preview: {
      handler: function(value) {
        let display = false
        for (const item of this.support) {
          if (this.mimeCheck(item)) {
            display = true
          }
        }
        this.thumbDisplay = display
      },
      immediate: true,
      deep: true
    }
  },
  methods: {
    mimeCheck(type) {
      return this.preview.mime.indexOf(type) !== -1
    },
    handleClick() {
      this.$emit('access', this.preview)
    }
  }
}
</script>

<style lang="scss" scoped>
.preview-container {
  height: 100%;
  .thumbnails {
    display: table;
    width: 100%;
    .box {
      display: table-cell;
      height: 225px;
      position: relative;
      vertical-align: middle;
      text-align: center;
      border-bottom: 1px solid #ebeef5;
    }
  }
  .content {
    font-size: 14px;
    line-height: 1.5;
    padding: 15px;
    .panel {
      word-wrap: break-word;
      label {
        display: block;
        text-transform: uppercase;
        color: #95a5a6;
        font-size: 13px;
        font-weight: 600;
        margin: 0 0 5px 0;
      }
      .name {
        margin: 0 0 5px;
      }
      table {
        font-size: 13px;
        border-collapse: collapse;
        text-align: left;
        th,
        td {
          padding: 4px 0 4px 0;
          vertical-align: top;
        }
        th {
          font-weight: 600;
          color: #95a5a6;
          padding-right: 15px;
          text-transform: uppercase;
        }
        td {
          color: #2b3e50;
          line-height: 1.5;
          word-wrap: break-word;
        }
        a {
          text-decoration: underline;
        }
      }
    }
  }
}
</style>
