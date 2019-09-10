<template>
  <section class="">

    <attachment-search-bar></attachment-search-bar>

    <div class="row">
      <div class="col-md-6">
        <attachment-atags></attachment-atags>
      </div>

      <div class="col-md-6">
        <attachments></attachments>
      </div>
    </div>
  </section>
</template>

<script>
import { mapState, mapGetters, mapActions } from 'vuex'
import attachment from '../js/store/store.js'

import SearchBar from './SearchBar.vue'
import Atags from './Atags.vue'
import Attachments from './Attachments.vue'

export default
{
  name: 'attachment-browse',
  components:
  {
    'attachment-search-bar':SearchBar,
    'attachment-atags':Atags,
    'attachments': Attachments
  },
  props: { aid: String, settings: Object },
  computed:
  {
    ...mapState('attachment',{
      aParams: 'aParams',
      tParams:'tParams'
    }),
  },
  watch:
  {
    aParams:
    {
      handler(){ this.fetchAttachments({config:{ params: this.aParams}}) },
      deep: true
    },
    tParams:
    {
      handler(){ this.fetchTags({config:{ params: this.tParams}}) },
      deep: true
    },
  },
  beforeCreate()
  {
    this.$store.registerModule('attachment', attachment)
  },
  created()
  {
    // create all we need
    this.createChannel(this.aid)
    this.aParams.uuid = this.aid //!!!!!!!!

    this.fetchTags()
    this.fetchAttachments({config:{ params: this.aParams}})
  },
  methods:
  {
    ...mapActions('attachment',{createChannel: 'createChannel'}),
    ...mapActions('attachment/atags',{fetchTags: 'fetchList'}),
    ...mapActions('attachment/attachments',{fetchAttachments: 'fetchList'}),
  }
}
</script>
