<template>
  <main class="section-attachment--container">
    <div class="">
      <transition name="fade">

        <!--- upload -->
        <section v-if="mode == 'upload'" class="section-attachment--upload">
          <div class="section__nav d-flex flex-row justify-content-between">
            <h1>Téléchager</h1>
            <div class="action">
              <button type="button" name="button" class="btn btn-error">FERMER</button>
              <button @click="mode = 'browse'" type="button" name="button" class="btn btn-danger">ANNULER</button>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3">
              <div class="section__nav d-flex flex-row justify-content-between">
                <h3 class="mb-0">Tags</h3>
              </div>
              <attachment-atags :aid="aid" :upload="true"></attachment-atags>
            </div>
            <div class="col-md-9">
              <attachment-upload :aid="aid"></attachment-upload>
            </div>
          </div>
        </section>

        <!-- browse mode -->
        <section v-if="mode == 'browse'" class="section-attachment--browse">
          <div class="section__nav d-flex flex-row justify-content-between">
            <h1>Fichiers</h1>
            <div class="action">
              <button type="button" name="button" class="btn btn-error">FERMER</button>
              <button @click="mode = 'upload'" type="button" name="button" class="btn btn-primary">TÉLÉCHARGER</button>
            </div>
          </div>
          <div class="row no-gutters">
            <div class="col-12">
              <div class="">
              </div>
              <attachment-search-bar :aid="aid"></attachment-search-bar>
              <div class="utils--spacer-semi"></div>
            </div>

            <div class="col-md-3">
              <div class="section__nav d-flex flex-row justify-content-between">
                <h3 class="mb-0">&nbsp;Filtres et tags</h3>
                <button type="button" name="button" class="btn mb-0 color--white"><i class="material-icons">view_module</i></button>

              </div>
              <div class="utils--spacer-semi"></div>
              <attachment-atags :aid="aid" :upload="false"></attachment-atags>
            </div>
            <div class="col-md-9">
              <attachments :aid="aid" :settings="settings"></attachments>
            </div>
          </div>
        </section>
      </transition>
    </div>
  </main>
</template>
<script>
// npm libs
import { mapState, mapGetters, mapActions } from 'vuex'
import createCrudModule from 'vuex-crud';

// js scripts
import { client, parseResponse, parseResponseWithPaginate, parseTags} from '../js/client.js'
import attachment from '../js/store/store.js'

// vue components
import SearchBar from './SearchBar.vue'
import Atags from './Atags.vue'
import Attachments from './Attachments.vue'
import Upload from './Upload.vue'



export default
{
  name: 'attachment-browse',
  components:
  {
    'attachment-search-bar':SearchBar,
    'attachment-atags':Atags,
    'attachment-upload':Upload,
    'attachments': Attachments,
  },
  props: { aid: String, settings: Object },
  data()
  {
    return {
      mode: 'browse'
    }
  },
  computed:
  {
    aParams()
    {
      return this.$store.get(this.aid + '/aParams')
    },
    tParams()
    {
      return this.$store.get(this.aid + '/tParams')
    }
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
      parseList: parseResponseWithPaginate,
      onFetchListSuccess: (o, response) => {
        this.$store.set(this.aid + '/pagination', response.pagination)
      },
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
