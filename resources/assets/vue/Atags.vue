<template lang="html">
  <section>
    <ul v-if="atagTypes">
      <li v-for="(atagType, index1) in atagTypes">
        <h3>{{atagType.name}}</h3>
        <div>
          <span
            class="badge"
            v-for="(atag, index2) in atagType.atags"
            @click="toggle(index1, index2)"
            :key="atag.id"
            :class="[atag.isActive ? 'badge-primary' : 'badge-secondary']"
            >{{atag.name}}</span>
        </div>
      </li>
    </ul>
  </section>
</template>

<script>
import { mapState, mapGetters, mapActions } from 'vuex'

export default
{
  name:'attachment-atags',
  props: { aid: String, upload:Boolean },
  computed:
  {
    atagTypes()
    {
      return this.$store.get(this.aid + '/atags/list')
    },
  },
  methods:
  {
    toggle(index1,index2)
    {
      // test exclusive
      if(this.atagTypes[index1].exclusive) for(let i in this.atagTypes[index1].atags) if(i != index2) this.atagTypes[index1].atags[i].isActive = false

      // toogle
      if(!this.atagTypes[index1].atags[index2].isActive) this.atagTypes[index1].atags[index2].isActive = true
      else this.atagTypes[index1].atags[index2].isActive = false

      // force render
      this.$forceUpdate()

      // loop and fetch attachment by mutating aParams.atags
      if(this.upload) return
      let atags = []
      for(let i1 in this.atagTypes)for(let i2 in this.atagTypes[i1].atags) if(this.atagTypes[i1].atags[i2].isActive) atags.push(this.atagTypes[i1].atags[i2].slug)
      this.$store.set(this.aid + '/aParams', Object.assign(
        this.$store.get(this.aid + '/aParams'),
        {
          atags: atags.join(','),
          page: 1
        }
      ))
    }
  }
}
</script>
