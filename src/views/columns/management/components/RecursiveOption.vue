<template>
  <span>
    <span v-for="(item,index) in list" :key="index">
      <el-option
        v-if="item.id !== ignore"
        :value="item.id"
        :label="item[labelName]"
        :checked="value === item.id"
      >{{ label(item) }}</el-option>
      <recursive-option
        v-if="item.id !== ignore && scopes[index]"
        :level="init+1"
        :list="item.children"
        :ignore="ignore"
      />
    </span>
  </span>
</template>
<script>
export default {
  name: 'RecursiveOption',
  props: {
    value: {
      // 当前选中的值
      type: Number,
      default: 0
    },
    list: {
      // 带子级菜单的列表
      type: Array,
      default() {
        return []
      }
    },
    ignore: {
      // 忽略的项，一般是其自身的ID
      // 递归时将会忽略指定ID与其所有子栏目
      type: Number,
      default: 0
    },
    labelName: {
      // 显示名称的键名
      type: String,
      default: 'title'
    },
    childName: {
      // 子列表键名
      type: String,
      default: 'children'
    },
    level: {
      // 递归列表起始层级
      type: Number,
      default: 0
    }
  },
  data() {
    return {
      init: this.level,
      selection: this.value,
      scopes: []
    }
  },
  watch: {
    list: {
      handler: function() {
        this.scope()
      },
      deep: true
    }
  },
  created() {
    this.scope()
  },
  methods: {
    label(row) {
      return '　'.repeat(this.level) + row[this.labelName]
    },
    scope() {
      this.list.forEach((item, index) => {
        this.scopes[index] = this.childName in item
      })
    }
  }
}
</script>
