<template>
  <section class="">

    <attachment-search-bar></attachment-search-bar>

    <div class="row">
      <div class="col-md-6">
        <ul v-if="atags">
          <li v-for="(atag, index) in atags" @click="toggle(index)">{{atag.name}}</li>
        </ul>
      </div>

      <div class="col-md-6">
        <ul v-if="attachments">
          <li v-for="attachment in attachments">{{attachment.name}}</li>
        </ul>
      </div>
    </div>
  </section>
</template>

<script>
import { mapState, mapGetters, mapActions } from 'vuex'
import attachment from '../js/store/store.js'

import search from './SearchBar.vue'

export default
{
  name: 'attachment-browse',
  components:
  {
    'attachment-search-bar':search
  },
  props: { aid: String, settings: Object },
  computed:
  {
    ...mapState('attachment',{aParams: 'aParams', tParams:'tParams' }),
    ...mapGetters('attachment/atags',{atags: 'list'}),
    ...mapGetters('attachment/attachments',{attachments: 'list'}),
  },
  watch:
  {
    aParams:
    {
      handler(){ this.fAList() },
      deep: true
    },
    tParams:
    {
      handler(){ this.fTList() },
      deep: true
    },
  },
  beforeCreate()
  {
    this.$store.registerModule('attachment', attachment)
  },
  created()
  {
    this.createChannel(this.aid)
    if(this.atags.length == 0) this.fTList()
    this.fAList()
  },
  methods:
  {
    ...mapActions('attachment',{createChannel: 'createChannel'}),
    ...mapActions('attachment/atags',{_fTList: 'fetchList'}),
    ...mapActions('attachment/attachments',{_fAList: 'fetchList'}),

    fAList(){ this._fAList({config:{ params: this.aParams}})},
    fTList(){ this._fTList({config:{ params: this.tParams}})},

    toggle(index)
    {
      let atags = []
      if(!this.atags[index].isActived) this.atags[index].isActived = true
      else this.atags[index].isActived = false
      for(let i in this.atags) if(this.atags[i].isActived) atags.push(this.atags[i].slug)
      this.$store.set('attachment/aParams.atags', atags.join(','))
    }
  }
}
</script>
