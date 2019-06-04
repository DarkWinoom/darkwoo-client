<template>
  <common-link :to="url"><slot /></common-link>
</template>
<script>
import { isExternal } from '@/utils/validate'
import CommonLink from '@/layout/components/Sidebar/Link'

export default {
  name: 'AppLink',
  components: {
    CommonLink
  },
  props: {
    to: {
      // 可以是router中定义的name，或者外部链接（自动识别）
      type: String,
      default: ''
    },
    id: {
      // {router}/{id}
      type: [Number, String],
      default: undefined
    },
    query: {
      // {router}/{id}?query
      type: Object,
      default: undefined
    }
  },
  data() {
    return {
      url: undefined
    }
  },
  created() {
    if (!isExternal(this.url)) {
      this.url = {
        name: this.to,
        params: { id: this.id },
        query: this.query
      }
    } else {
      this.url = this.to
    }
  }
}
</script>
