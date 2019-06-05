<template>
  <div :style="{height:height+'px',zIndex:zIndex}">
    <div
      class="sub-navbar"
      :style="{top:(isSticky ? stickyTop +'px' : ''),zIndex:zIndex,position:position,width:width,height:height+'px'}"
    >
      <div class="button">
        <app-link v-if="backRoute" :to="backRoute">
          <el-button size="medium" icon="el-icon-back">返回</el-button>
        </app-link>
        <el-button size="medium" type="success" icon="el-icon-check" @click="submit">提交</el-button>
      </div>
    </div>
  </div>
</template>

<script>
import AppLink from '@/components/AppLink'

export default {
  name: 'SubmitBar',
  components: {
    AppLink
  },
  props: {
    backRoute: {
      type: String,
      default: ''
    },
    stickyTop: {
      type: Number,
      default: 84
    },
    zIndex: {
      type: Number,
      default: 10
    }
  },
  data() {
    return {
      active: false,
      position: '',
      width: undefined,
      height: undefined,
      isSticky: false
    }
  },
  mounted() {
    this.height = this.$el.getBoundingClientRect().height
    window.addEventListener('scroll', this.handleScroll)
    window.addEventListener('resize', this.handleResize)
  },
  activated() {
    this.handleScroll()
  },
  destroyed() {
    window.removeEventListener('scroll', this.handleScroll)
    window.removeEventListener('resize', this.handleResize)
  },
  methods: {
    submit() {
      this.$emit('submit')
    },
    sticky() {
      if (this.active) {
        return
      }
      this.position = 'fixed'
      this.active = true
      this.width = this.width + 'px'
      this.isSticky = true
    },
    handleReset() {
      if (!this.active) {
        return
      }
      this.reset()
    },
    reset() {
      this.position = ''
      this.width = 'auto'
      this.active = false
      this.isSticky = false
    },
    handleScroll() {
      const width = this.$el.getBoundingClientRect().width
      this.width = width || 'auto'
      const offsetTop = this.$el.getBoundingClientRect().top
      if (offsetTop < this.stickyTop) {
        this.sticky()
        return
      }
      this.handleReset()
    },
    handleResize() {
      if (this.isSticky) {
        this.width = this.$el.getBoundingClientRect().width + 'px'
      }
    }
  }
}
</script>
<style lang="scss" scoped>
.sub-navbar {
  height: 50px;
  line-height: 50px;
  position: relative;
  width: 100%;
  text-align: right;
  padding-right: 20px;
  transition: 600ms ease position;
  background: rgba(0, 0, 0, 0.15);

  .subtitle {
    font-size: 20px;
    color: #fff;
  }

  &.draft {
    background: #d0d0d0;
  }

  &.deleted {
    background: #d0d0d0;
  }
}
</style>
