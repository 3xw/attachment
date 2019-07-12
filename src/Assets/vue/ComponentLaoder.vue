<template>
  <component :is="componentInstance" v-bind="attributes" />
</template>
<script>
export default {
  props: {
    name: { type: String, default: 'null' },
    props: { type: String }
  },
  data: function(){
    return {
      attributes: JSON.parse(this.props)
    }
  },
  computed: {
    componentInstance () {
      if (this.name == 'null') {
         return null
      }
      const name = this.name
      return () => import(/* webpackChunkName: "[request]" */ `./${name}.vue`)
    }
  }
}
</script>
