<template lang="html">
  <div class="">

    <!-- INPUT VIEW -->
    <div class="input form-group">
      <label>{{settings.label}}</label>
      <div class="attachment-input">
          <div class="btn-group" data-intro="Ajouter des médias à l'aide de ces boutons" data-position="right">
            <button type="button" class="btn btn-fill btn-xs btn-info" @click="mode = 'upload'">
              <i class="fa fa-cloud-upload" aria-hidden="true"></i>
              Upload
            </button>
            <button type="button" class="btn btn-fill btn-xs btn-info" @click="mode = 'browse'">
              <i class="fa fa-cloud" aria-hidden="true"></i>
              Browse
            </button>
            <button v-if="dispalyEmbed()" type="button" class="btn btn-fill btn-xs btn-info" @click="mode = 'embed'">
              <i class="fa fa-code" aria-hidden="true"></i>
              Add an embed code
            </button>
          </div>

        <!-- files
        <attachment-files :aid="aid" :settings="settings" ></attachment-files>
        -->

      </div>
    </div><!-- // END INPUT VIEW -->

    <!-- OVERLAY FULL -->
    <div v-if="mode != 'input'" class="attachment-overlay-full">
      <div class="d-flex flex-row justify-content-between align-items-center">
        <!--<h1>Ajouter des fichiers</h1>-->
        <button @click="mode = 'input';" type="button" name="button" class="btn btn-danger">ANNULER</button>
      </div>

      <div class="utils--spacer-semi"></div>
      <div class="row">
        <div class="col-12 col-md-3">
          <label>Tags</label>
          <attachment-atags :aid="aid" :upload="true" :filters="settings.browse.filters" :options="settings.options"></attachment-atags>
        </div>
        <div class="col-12 col-md-9">

          <!-- upload -->
          <attachment-upload v-if="mode == 'upload'" :aid="aid"></attachment-upload>

          <!-- browse
          <attachment-browse :aid="aid" :types="types" :tags="tags" :settings="settings" :from="'input'"></attachment-browse>
          -->

          <!-- embed
          <attachment-embed :aid="aid" :settings="settings" ></attachment-embed>
          -->
        </div>
      </div>
    </div><!-- // END OVERLAY FULL -->

  </div>
</template>

<script>
// npm libs
import { mapState, mapGetters, mapActions } from 'vuex'
import createCrudModule from 'vuex-crud';

// js scripts
import { client, parseResponse, parseResponseWithPaginate, parseTags} from '../js/client.js'
import attachment from '../js/store/store.js'

// vue components
import Upload from './Upload.vue'
import Embed from './Embed.vue'



export default
{
  name: 'attachment-input',
  components:
  {
    'attachment-embed': Embed,
    'attachment-upload': Upload,
  },
  props: { aid: String, settings: Object },
  data()
  {
    return {
      mode: 'input',
      loading: false
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
  },
  created()
  {
    //Check role
    if(!this.settings.role) this.settings.role = 'user'

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
      onFetchListStart: () => {
        this.loading = true
      },
      onFetchListSuccess: (o, response) => {
        this.loading = false
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
    this.$store.registerModule(this.aid+'/aarchives', createCrudModule({
      resource: 'aarchives',
      urlRoot: this.settings.url+'attachment/aarchives',
      client,
      parseSingle: parseResponse,
      parseList: parseTags,
      onFetchListSuccess: (o, response) => {
        this.$store.set(this.aid + '/aarchives', response.data)
      },
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

    //this.fetchAarchives();

    // set uuid & fetch data ( all in one because of deep watching )
    this.aParams.uuid = this.tParams.uuid = this.aid
  },
  methods:
  {
    dispalyEmbed()
    {
      for( var type in this.settings.types ) if(this.settings.types[type].indexOf('embed') != -1) return true
      return false
    }
  }
}
</script>
