<template>
  <div>
    <div v-if="enableFixed" class="fixed" />
    <div :class="{'fixed-navigation':enableFixed}">
      <div :class="{'hidden':hidden}" class="pagination-container">
        <el-pagination
          :background="background"
          :current-page.sync="currentPage"
          :page-size.sync="pageSize"
          :layout="layout"
          :page-sizes="pageSizes"
          :total="total"
          v-bind="$attrs"
          :small="isMobile"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { scrollTo } from '@/utils/scrollTo'

export default {
  name: 'Pagination',
  props: {
    enableFixed: {
      type: Boolean,
      default: true
    },
    total: {
      required: true,
      type: Number
    },
    page: {
      type: Number,
      default: 1
    },
    limit: {
      type: Number,
      default: 20
    },
    pageSizes: {
      type: Array,
      default() {
        return [10, 20, 30, 50, 100]
      }
    },
    layout: {
      type: String,
      default: 'total, sizes, prev, pager, next, jumper'
    },
    background: {
      type: Boolean,
      default: true
    },
    autoScroll: {
      type: Boolean,
      default: true
    },
    hidden: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      currentPage: this.page,
      pageSize: this.limit
    }
  },
  computed: {
    isMobile() {
      return this.$store.state.app.device === 'mobile'
    }
  },
  methods: {
    handleSizeChange(val) {
      this.$emit('pagination', {
        page: this.currentPage,
        limit: val
      })
      if (this.autoScroll) {
        scrollTo(0, 800)
      }
    },
    handleCurrentChange(val) {
      this.$emit('pagination', {
        page: val,
        limit: this.pageSize
      })
      if (this.autoScroll) {
        scrollTo(0, 800)
      }
    }
  }
}
</script>

<style lang="scss">
.app-wrapper {
  &.hideSidebar {
    .fixed-navigation {
      left: 54px;
    }
  }
  &.mobile {
    .fixed-navigation {
      left: 0;
    }
  }
}
</style>

<style lang="scss" scoped>
.fixed {
  height: 62px;
}
.fixed-navigation {
  position: fixed;
  z-index: 99;
  left: 210px;
  right: 0;
  bottom: 0;
  border-top: 1px solid #d0d0d0;
  background: #fff;
  transition: left 0.28s;
  transform: translateZ(0);
  .pagination-container {
    padding: 15px 20px;
    margin: 0;
    background: #fff;
    text-align: right;
    &.hidden {
      display: none;
    }
  }
}
</style>
