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
  created: function()
  {
    console.log(this.attributes);
  },
  computed: {
    componentInstance () {
      if (this.name == 'null') {
        return null
      }
      const name = this.camelize(this.name.substring(this.name.indexOf('-') + 1))
      return () => import(/* webpackChunkName: "[request]" */ `./${name}.vue`)
    }
  },
  methods:
  {
    camelize(str) {
      return str.replace(/(?:^\w|[A-Z]|\b\w)/g, function(word, index)
      {
        return word.toUpperCase();
      }).replace(/\s+/g, '');
    }
  }
}
</script>
