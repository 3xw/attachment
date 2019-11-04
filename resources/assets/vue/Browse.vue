<template>
  <main class="section-attachment--container">
    <div class="">
      <transition name="fade">

        <!--- upload -->
        <section v-if="mode == 'upload'" class="section-attachment--upload">
          <div class="row">
            <div class="col-md-12">
              <div class="section__nav">
                <div class="d-flex flex-row justify-content-between align-items-center">
                  <h1>Ajouter des fichiers</h1>
                  <button @click="mode = 'browse';" type="button" name="button" class="btn btn-danger">ANNULER</button>
                </div>
                <div class="utils--spacer-semi"></div>
                <div class="row">
                  <div class="col-12 col-md-3">
                    <label>Tags</label>
                    <attachment-atags :aid="aid" :upload="true" :filters="settings.browse.filters" :options="settings.options"></attachment-atags>
                  </div>
                  <div class="col-12 col-md-9">
                    <attachment-upload :aid="aid"></attachment-upload>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- browse mode -->
        <section v-if="mode == 'browse'" class="section-attachment--browse">
          <div class="row no-gutters">
            <div class="w-100"></div>
            <div class="col-md-3 col-xl-2">
              <div class="section__side">
                <div class="section__add section--blue-light color--blue-dark action pointer d-flex flex-row align-items-center" @click="mode = 'upload';$forceUpdate();">
                    <icon-add></icon-add>&nbsp;&nbsp;&nbsp;&nbsp;<p class="mb-0">Ajouter des fichiers</p>
                </div>
                <div class="section__nav">
                  <div class="d-flex flex-row align-items-center">
                    <icon-filter></icon-filter>&nbsp;&nbsp;&nbsp;&nbsp;<p class="mb-0">Filtres et tags</p>
                  </div>
                  <div class="utils--spacer-semi"></div>
                  <attachment-atags :aid="aid" :upload="false" :filters="settings.browse.filters" :options="settings.options"></attachment-atags>
                </div>
              </div>
            </div>
            <div class="col-md-9 col-xl-10">
              <attachments :aid="aid" :settings="settings"></attachments>
            </div>
          </div>
        </section>

        <!-- edit mode -->
        <section v-if="mode == 'edit'" class="section-attachment--upload">
          <div class="row">
            <div class="col-md-12">
              <div class="section__nav">
                <div class="d-flex flex-row justify-content-between align-items-center">
                  <h1>Editer des fichiers</h1>
                  <button @click="mode = 'browse';" type="button" name="button" class="btn btn-danger">ANNULER</button>
                </div>
                <div class="utils--spacer-semi"></div>
                <div class="row">
                  <div class="col-12 col-md-3">
                    <label>Tags</label>
                    <attachment-atags :aid="aid" :upload="true" :filters="settings.browse.filters" :options="settings.options"></attachment-atags>
                  </div>
                  <div class="col-12 col-md-9">
                    <attachment-edit :aid="aid" :settings="settings"></attachment-edit>
                  </div>
                </div>
              </div>
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

import iconFilter from './icons/filter.vue'
import iconAdd from './icons/add.vue'

// vue components
import Atags from './Atags.vue'
import Attachments from './Attachments.vue'
import Upload from './Upload.vue'
import Edit from './Edit.vue'



export default
{
  name: 'attachment-browse',
  components:
  {
    'attachment-atags': Atags,
    'attachment-upload': Upload,
    'attachment-edit': Edit,
    'attachments': Attachments,
    'icon-add': iconAdd,
    'icon-filter': iconFilter
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
    },
    selectedFiles()
    {
      return this.$store.get(this.aid + '/selection.files')
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
    selectedFiles(value)
    {
      this.createSelectedFilesToken({data: {files: value}})
    },
    mode: function(){
      this.$forceUpdate()
      if(this.mode == 'browse'){
        this.$store.set(this.aid + '/aParams', Object.assign(this.$store.get(this.aid + '/aParams'),{ atags: '', upload: 0, refresh: new Date().getTime(), page: 1 }))
      }else{
        this.$store.set(this.aid + '/aParams', Object.assign(this.$store.get(this.aid + '/aParams'),{ upload: 1 }))
      }
    }
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
      urlRoot: this.settings.url+'attachment/attachments',
      client,
      parseSingle: parseResponse,
      parseList: parseResponseWithPaginate,
      onFetchListSuccess: (o, response) => {
        this.$store.set(this.aid + '/pagination', response.pagination)
      },
    }))
    this.$store.registerModule(this.aid+'/atags', createCrudModule({
      resource: 'atags',
      urlRoot: this.settings.url+'attachment/atags',
      client,
      parseSingle: parseResponse,
      parseList: parseTags
    }))
    this.$store.registerModule(this.aid+'/token', createCrudModule({
      resource: 'token',
      only: ['CREATE'],
      urlRoot: this.settings.url+'attachment/download/get-zip-token',
      client,
      idAttribute: 'token',
      onCreateSuccess: (o, response) => {
        this.$store.set(this.aid + '/selection.token', response.data.token)
      },
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
      },
      createSelectedFilesToken(dispatch, payload)
      {
        return dispatch(this.aid + '/token/create', payload)
      },
    })
  }
}
</script>
