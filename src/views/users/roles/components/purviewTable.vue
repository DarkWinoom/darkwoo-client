<template>
  <el-table
    :data="formatData"
    :row-style="showRow"
    v-bind="$attrs"
    row-key="id"
  >
    <el-table-column label="功能" min-width="200">
      <template slot-scope="scope">
        <span v-for="space in scope.row._level" :key="space" class="ms-tree-space" />
        <span v-if="iconShow(scope.row)" class="tree-ctrl" @click="toggleExpanded(scope.$index)">
          <i v-if="!scope.row._expanded" class="el-icon-plus" />
          <i v-else class="el-icon-minus" />
        </span>
        {{ scope.row.name }}
      </template>
    </el-table-column>
    <el-table-column label="权限" align="center" width="340">
      <template slot-scope="scope">
        <el-checkbox-group v-model="temp[scope.row.id]" @change="handleChange(scope.row.id)">
          <template v-for="item in purview">
            <el-checkbox
              v-if="settingShowCheck(scope.row, item.value)"
              :key="item.value"
              :label="item.value"
              @change="statusCheck(scope.row.id)"
            >
              {{ item.name }}
            </el-checkbox>
          </template>
        </el-checkbox-group>
      </template>
    </el-table-column>
    <el-table-column align="center" width="80">
      <template slot="header" slot-scope="scope">
        <span v-show="false">{{ scope.row ? 1 : 0 }}</span>
        <el-checkbox
          v-model="selection.allStatus"
          class="select-all"
          title="全选"
          @change="handleSelectionAll()"
        />
      </template>
      <template slot-scope="scope">
        <el-checkbox
          v-show="false"
          v-model="selection.allStatus"
        />
        <el-checkbox
          v-model="selection.status[scope.row.id]"
          :indeterminate="selection.indeterminate[scope.row.id]"
          class="select-line"
          title="选择一行"
          @change="handleSelectionLine(scope.row.id)"
        />
      </template>
    </el-table-column>
    <slot />
  </el-table>
</template>

<script>
import treeToArray from './eval'

export default {
  name: 'TreeTable',
  props: {
    data: {
      type: [Array, Object],
      required: true
    },
    form: {
      type: [Array, Object],
      default() {
        return []
      }
    },
    expandAll: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      init: false,
      selection: {
        status: [],
        indeterminate: [],
        purview: [],
        allStatus: false
      },
      temp: [],
      purview: [
        {
          name: '访问',
          value: '1'
        },
        {
          name: '新增',
          value: '2'
        },
        {
          name: '修改',
          value: '4'
        },
        {
          name: '删除',
          value: '8'
        }
      ]
    }
  },
  computed: {
    // 格式化数据源
    formatData: function() {
      let tmp
      if (!Array.isArray(this.data)) {
        tmp = [this.data]
      } else {
        tmp = this.data
      }
      const args = [tmp, this.expandAll]
      return treeToArray.apply(null, args)
    }
  },
  watch: {
    formatData: {
      handler: function(value) {
        if (value.length > 0 && !this.init) {
          for (const item of value) {
            if (this.form[item.id]) {
              this.temp[item.id] = this.form[item.id]
            } else {
              this.temp[item.id] = []
            }
            this.statusCheck(item.id)
          }
          this.init = true
        }
      },
      deep: true,
      immediate: true
    }
  },
  methods: {
    statusCheck(id) {
      // console.log(this.data.filter(item => parseInt(item.id) === parseInt(id)))
      const purviewLength = this.purview.length
      const item = this.temp[id]
      this.selection.status[id] = item.length >= purviewLength
      this.selection.indeterminate[id] = item.length > 0 && item.length < purviewLength
    },
    showRow: function(row) {
      const show = (row.row.parent ? (row.row.parent._expanded && row.row.parent._show) : true)
      row.row._show = show
      return show ? '' : 'display:none;'
    },
    settingShowCheck(row, pValue) {
      // 检测权限是否开放设置（从后端获取）
      if (row.disable || row.enable) {
        var check = []
        let disable = true
        if (row.disable) {
          for (const index in row.disable) {
            check.push(row.disable[index].toString())
          }
        } else {
          disable = false
          for (const index in row.enable) {
            check.push(row.enable[index].toString())
          }
        }
        return check.includes(pValue) ? !disable : disable
      } else {
        return true
      }
    },
    // 切换下级是否展开
    toggleExpanded: function(trIndex) {
      const record = this.formatData[trIndex]
      record._expanded = !record._expanded
    },
    // 图标显示
    iconShow(record) {
      return (record.list && record.list.length > 0)
    },
    handleSelectionAll() {
      const temp = { ...this.temp }
      for (const id in temp) {
        if (this.selection.allStatus) {
          this.temp[id] = this.purview.map((item) => {
            return item.value
          })
        } else {
          this.temp[id] = []
        }
        this.statusCheck(id)
      }
    },
    handleSelectionLine(id) {
      const status = this.selection.status[id]
      if (status) {
        this.temp[id] = this.purview.map((item) => {
          return item.value
        })
      } else {
        this.temp[id] = []
      }
      this.statusCheck(id)
    },
    handleChange(id) {
      this.$emit('change', this.temp)
    }
  }
}
</script>
<style rel="stylesheet/css">
  @keyframes treeTableShow {
    from {opacity: 0;}
    to {opacity: 1;}
  }
  @-webkit-keyframes treeTableShow {
    from {opacity: 0;}
    to {opacity: 1;}
  }
</style>

<style lang="scss" rel="stylesheet/scss" scoped>
  $color-blue: #2196F3;
  $space-width: 18px;
  .ms-tree-space {
    position: relative;
    top: 1px;
    display: inline-block;
    font-style: normal;
    font-weight: 400;
    line-height: 1;
    width: $space-width;
    height: 14px;
    &::before {
      content: ""
    }
  }
  .processContainer{
    width: 100%;
    height: 100%;
  }
  table td {
    line-height: 26px;
  }

  .tree-ctrl{
    position: relative;
    cursor: pointer;
    color: $color-blue;
    margin-left: -$space-width;
  }
</style>
