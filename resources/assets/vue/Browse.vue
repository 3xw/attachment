<template>
  <section class="">

    <attachment-search-bar :aid="aid"></attachment-search-bar>

    <div class="row">
      <div class="col-md-6">
        <attachment-atags :aid="aid"></attachment-atags>
      </div>

      <div class="col-md-6">
        <attachments :aid="aid"></attachments>
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
    aParams()
    {
      return this.$store.get(this.aid + '/aParams')
    },
    tParams()
    {
      return this.$store.get(this.aid + '/tParams')
    },
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
  created()
  {
    // create new module
    this.$store.registerModule(this.aid, Object.assign({}, attachment))

    // set uuid & fetch data ( all in one because of deep watching )
    this.aParams.uuid = this.tParams.uuid = this.aid
  },
  methods:
  {
    ...mapActions({
      fetchAttachments(dispatch, payload)
      {
        return dispatch(this.aid + '/attachments/fetchList', payload)
      },
      fetchTags(dispatch, payload)
      {
        return dispatch(this.aid + '/atags/fetchList', payload)
      }
    })
  }
}
</script>
