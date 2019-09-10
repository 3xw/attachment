<template lang="html">
  <section>
    <ul v-if="atags">
      <li v-for="(atag, index) in atags" @click="toggle(index)">{{atag.name}}</li>
    </ul>
  </section>
</template>

<script>
import { mapState, mapGetters, mapActions } from 'vuex'

export default
{
  name:'attachment-atags',
  props: { aid: String },
  computed:
  {
    atags()
    {
      return this.$store.get(this.aid + '/atags/list')
    },
  },
  methods:
  {
    toggle(index)
    {
      let atags = []
      if(!this.atags[index].isActived) this.atags[index].isActived = true
      else this.atags[index].isActived = false
      for(let i in this.atags) if(this.atags[i].isActived) atags.push(this.atags[i].slug)
      this.$store.set(this.aid + '/aParams.atags', atags.join(','))
    }
  }
}
</script>
