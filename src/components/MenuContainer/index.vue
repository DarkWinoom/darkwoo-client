<template>
  <div class="menu-container">
    <div class="left-menu">
      <app-link v-if="appLink.name" :to="appLink.route">
        <el-button
          class="menu-item"
          size="small"
          type="success"
          icon="el-icon-plus"
        >{{ appLink.name }}</el-button>
      </app-link>
      <div class="inline-block">
        <slot name="left-menu" />
      </div>
    </div>
    <div class="right-menu">
      <div class="inline-block">
        <slot name="right-menu" />
      </div>
      <batch-operation
        v-model="hasBatch"
        :sequence="batch.sequence"
        :enable="batch.enable"
        :disable="batch.disable"
        :remove="batch.delete"
      />
    </div>
  </div>
</template>
<script>
import AppLink from '@/components/AppLink'
import BatchOperation from './Batch'

export default {
  name: 'MenuContainer',
  components: {
    AppLink,
    BatchOperation
  },
  props: {
    appLink: {
      // 最左侧的按钮
      type: Object,
      default() {
        return {
          route: '',
          name: ''
        }
      }
    },
    batch: {
      // 最右批量按钮
      type: Object,
      default() {
        return {
          sequence: '',
          enable: '',
          disable: '',
          delete: ''
        }
      }
    }
  },
  data() {
    return {
      empty: ''
    }
  },
  computed: {
    hasBatch() {
      return !!(
        this.batch.sequence ||
        this.batch.enable ||
        this.batch.disable ||
        this.batch.delete
      )
    }
  }
}
</script>
<style lang="scss" scoped>
.menu-container {
  font-size: 15px;
  clear: both;

  .inline-block{
    display: inline-block;
  }

  .left-menu {
    float: left;
    padding-bottom: 20px;
    .menu-item {
      display: inline-block;
    }
  }
  .right-menu {
    float: right;
    padding-bottom: 20px;
  }

  &.bottom {
    padding: 20px 0 0;
  }
}
</style>
