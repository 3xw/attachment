<template>
  <section>
    browse
  </section>
</template>

<script>
import { mapState, mapGetters, mapActions } from 'vuex'
import attachment from '../js/store/modules/attachment.js'

export default
{
  name: 'attachment-browse',
  data: function()
  {
    return {}
  },
  props: {
    aid: String,
    settings: Object
  },
  beforeCreate()
  {
    this.$store.registerModule('attachment', attachment)
  },
  created()
  {
    this.init({id:this.aid})
    console.log(this.$store.get('attachment/app'));
  },
  methods:
  {
    ...mapActions('attachment',{
      init: 'init'
    }),
  }
}
</script>
