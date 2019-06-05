<template>
  <div class="app-container">
    <el-tabs v-loading="loading" tab-position="top">
      <el-tab-pane v-for="item in list" :key="item.id" :label="item.title">
        <el-collapse accordion>
          <el-collapse-item v-for="child in item.item" :key="child.id" :title="child.title">
            <div v-html="child.content" />
          </el-collapse-item>
        </el-collapse>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>
<script>
export default {
  data() {
    return {
      loading: true,
      list: [
        {
          id: 1,
          title: '基本功能',
          item: [
            {
              id: 1,
              title: '一致性 Consistency',
              content: `<div>与现实生活一致：与现实生活的流程、逻辑保持一致，遵循用户习惯的语言和概念；</div>
            <div>在界面中一致：所有的元素和结构需保持一致，比如：设计样式、图标和文本、元素的位置等。</div>`
            },
            {
              id: 2,
              title: '反馈 Feedback',
              content: `<div>控制反馈：通过界面样式和交互动效让用户可以清晰的感知自己的操作；</div>
            <div>页面反馈：操作后，通过页面元素的变化清晰地展现当前状态。</div>`
            },
            {
              id: 3,
              title: '效率 Efficiency',
              content: `<div>简化流程：设计简洁直观的操作流程；</div>
            <div>清晰明确：语言表达清晰且表意明确，让用户快速理解进而作出决策；</div>
            <div>帮助用户识别：界面简单直白，让用户快速识别而非回忆，减少用户记忆负担。</div>`
            },
            {
              id: 4,
              title: '可控 Controllability',
              content: `<div>用户决策：根据场景可给予用户操作建议或安全提示，但不能代替用户进行决策；</div>
            <div>结果可控：用户可以自由的进行操作，包括撤销、回退和终止当前操作等。</div>`
            }
          ]
        },
        {
          id: 2,
          title: '页面介绍',
          item: [
            {
              id: 1,
              title: '反馈 Feedback',
              content: `<div>控制反馈：通过界面样式和交互动效让用户可以清晰的感知自己的操作；</div>
            <div>页面反馈：操作后，通过页面元素的变化清晰地展现当前状态。</div>`
            }
          ]
        },
        {
          id: 3,
          title: '角色管理',
          item: [
            {
              id: 1,
              title: '效率 Efficiency',
              content: `<div>简化流程：设计简洁直观的操作流程；</div>
            <div>清晰明确：语言表达清晰且表意明确，让用户快速理解进而作出决策；</div>
            <div>帮助用户识别：界面简单直白，让用户快速识别而非回忆，减少用户记忆负担。</div>`
            }
          ]
        },
        {
          id: 4,
          title: '定时任务补偿',
          item: [
            {
              id: 1,
              title: '可控 Controllability',
              content: `<div>用户决策：根据场景可给予用户操作建议或安全提示，但不能代替用户进行决策；</div>
            <div>结果可控：用户可以自由的进行操作，包括撤销、回退和终止当前操作等。</div>`
            }
          ]
        }
      ]
    }
  },
  created() {
    this.$nextTick(() => {
      this.fetchList()
    })
  },
  methods: {
    fetchList() {
      this.loading = true
      setTimeout(() => {
        this.loading = false
      }, 300)
    }
  }
}
</script>
