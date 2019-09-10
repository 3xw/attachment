<template>
  <section class="">
    <div class="row">

      <div class="col-12">
        <attachment-search-bar :aid="aid"></attachment-search-bar>
      </div>

      <div class="col-md-3">
        <attachment-atags :aid="aid"></attachment-atags>
      </div>

      <div class="col-md-9">
        <attachments :aid="aid"></attachments>
      </div>
    </div>
  </section>
</template>

<script>
// npm libs
import { mapState, mapGetters, mapActions } from 'vuex'
import createCrudModule from 'vuex-crud';

// js scripts
import { client, parseResponse, parseTags } from '../js/client.js'
import attachment from '../js/store/store.js'

// vue components
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
    // create new module and store settings
    this.$store.registerModule(this.aid, Object.assign({}, attachment))
    this.$store.set(this.aid + '/settings', this.settings)

    // CRUD
    client.baseURL = this.settings.url
    this.$store.registerModule(this.aid+'/attachments', createCrudModule({
      resource: 'attachments',
      urlRoot: '../attachment/attachments',
      client,
      parseSingle: parseResponse,
      parseList: parseResponse
    }))
    this.$store.registerModule(this.aid+'/atags', createCrudModule({
      resource: 'atags',
      urlRoot: '../attachment/atags',
      client,
      parseSingle: parseResponse,
      parseList: parseTags
    }))

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
