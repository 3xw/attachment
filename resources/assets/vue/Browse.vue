<template>
  <main >

    <!-- INPUT VIEW -->
    <div v-if="mode == 'input'" class="input form-group">
      <label>{{settings.label}}</label>
      <div class="attachment-input">

        <!-- btn -->
        <div class="btn-group" data-intro="Ajouter des médias à l'aide de ces boutons" data-position="right">
          <button type="button" class="btn btn-fill btn-xs btn-info" @click="mode = 'browse'">
            <i class="fa fa-cloud" aria-hidden="true"></i>Browse
          </button>
        </div>

        <!-- files -->
        <div class="row">
          <div v-for="(attachment, i ) in selectedFiles" :key="i" class="col-12" :class="settings.cols">
            <attachment :index="i" :aid="aid" mode="input" :attachment="attachment" :settings="settings"></attachment>
          </div>
          <div v-if="selectedFiles.length == 0">
            <input v-if="settings.relation == 'belongsTo'" type="hidden" :name="settings.field" value="">
            <input v-if="settings.relation != 'belongsTo'" type="hidden" name="attachments[]" value="">
          </div>
        </div>


      </div>
    </div><!-- // END INPUT VIEW -->

    <!-- BROWSE UPLOAD EMBED VIEW -->
    <div v-if="mode != 'input'" class="section-attachment--container" :class="{ 'attachment-overlay-full': settings.overlay, 'd-none': (mode == 'hidden') }">

      <!-- dissmiss -->
      <section v-if="settings.overlay" class="">
        <div class="text-right">
          <button @click="mode = (tinymce)? 'hidden' : 'input'" type="button" name="button" class="btn btn-danger">FERMER</button>
        </div>
      </section>

      <!-- browse mode -->
      <section v-if="mode == 'browse'" class="section-attachment--browse">
        <div class="row no-gutters">
          <div class="w-100"></div>
          <div class="col-md-3 col-xl-2">
            <div class="section__side">
              <div v-if="settings.groupActions.indexOf('add') != -1" class="section__add section--blue-light color--blue-dark action pointer d-flex flex-row align-items-center" @click="mode = 'upload';$forceUpdate();">
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

      <!--- upload -->
      <section v-if="mode == 'upload'" class="section-attachment--upload">
        <div class="row">
          <div class="col-md-12">
            <div class="section__nav">
              <div class="d-flex flex-row justify-content-between align-items-center">
                <h1>Ajouter des fichiers</h1>

                <div class="btn-group">

                  <!-- EMBED -->
                  <button
                  v-if="reIndexOf(settings.types, /embed/gm) != -1 && settings.groupActions.indexOf('tinymce') == -1"
                  @click="mode = 'embed'"
                  type="button"  name="button" class="btn btn--blue color--white">
                  AJOUTER UN CODE EMBED
                </button>

                <button @click="mode = 'browse'" type="button" name="button" class="btn btn-danger">ANNULER</button>
              </div>

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

    <!--- embed -->
    <section v-if="mode == 'embed'" class="section-attachment--embed">
      <div class="row">
        <div class="col-md-12">
          <div class="section__nav">
            <div class="d-flex flex-row justify-content-between align-items-center">
              <h1>Ajouter un code embed</h1>

              <div class="btn-group">

                <!-- UPLOAD -->
                <button
                @click="mode = 'upload'"
                type="button"  name="button" class="btn btn--blue color--white">
                UPLOADER UN FICHIER
              </button>

              <button @click="mode = 'browse'" type="button" name="button" class="btn btn-danger">ANNULER</button>
            </div>

          </div>
          <div class="utils--spacer-semi"></div>
          <div class="row">
            <div class="col-12 col-md-3">
              <label>Tags</label>
              <attachment-atags :aid="aid" :upload="true" :filters="settings.browse.filters" :options="settings.options"></attachment-atags>
            </div>
            <div class="col-12 col-md-9">
              <attachment-embed :aid="aid"></attachment-embed>
            </div>
          </div>
        </div>
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

  <!-- TinyMCE Options inline-options -->
  <section v-if="mode == 'editor-options'" class="section-attachment--editor-options">
    <inline-options :file="(selectedFiles)? selectedFiles[0] : false" :aid="aid" :settings="settings"></inline-options>
  </section>

</div><!-- // END BROWSE UPLOAD EMBED VIEW -->

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
import Atags from './Atags.vue'
import Attachment from './Attachment.vue'
import Attachments from './Attachments.vue'
import InlineOptions from './InlineOptions.vue'
import Upload from './Upload.vue'
import Embed from './Embed.vue'
import Edit from './Edit.vue'
import iconFilter from './icons/filter.vue'
import iconAdd from './icons/add.vue'


export default
{
  name: 'attachment-browse',
  components:
  {
    'attachment-atags': Atags,
    'attachment-upload': Upload,
    'attachment-embed': Embed,
    'attachment-edit': Edit,
    'attachments': Attachments,
    'attachment': Attachment,
    'inline-options': InlineOptions,
    'icon-add': iconAdd,
    'icon-filter': iconFilter,
  },
  props: { aid: String, settings: Object },
  data()
  {
    return {
      mode: 'browse',
      overlay: false,
      loading: false,
      tinymce: false,
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
      handler(){
        if(this.mode == 'browse'){
          this.fetchAttachments({config:{ params: this.aParams}})
        }
      },
      deep: true
    },
    tParams:
    {
      handler(){ this.fetchTags({config:{ params: this.tParams}}) },
      deep: true
    },
    selectedFiles(value)
    {
      let ids = []
      for(let i = 0;i < value.length;i++){
        ids.push(value[i].id)
      }
      //this.createSelectedFilesToken({data: {files: ids}})
    },
    mode()
    {
      this.$forceUpdate()

      switch(this.mode)
      {
        case 'browse':
        this.$store.set(this.aid + '/aParams', Object.assign(this.$store.get(this.aid + '/aParams'),{ atags: '', upload: 0, refresh: new Date().getTime(), page: 1 }))
        break

        default:
        this.$store.set(this.aid + '/aParams', Object.assign(this.$store.get(this.aid + '/aParams'),{ upload: 1 }))
      }
    }
  },
  created()
  {

    //Remove Char who breaks store
    this.aid = this.aid.replace(/\./g, '')

    // init
    this.overlay = this.settings.overlay
    this.mode = this.settings.mode

    // create new module and store settings
    this.$store.registerModule(this.aid, Object.assign({}, attachment))
    this.$store.set(this.aid + '/settings', this.settings)


    if(this.mode == 'input' && this.settings.attachments.length > 0)
      this.$store.set(this.aid + '/selection.files', this.settings.attachments)

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
    this.$store.registerModule(this.aid+'/aarchives', createCrudModule({
      resource: 'aarchives',
      only: ['CREATE'],
      urlRoot: this.settings.url+'attachment/aarchives',
      client,
      parseSingle: parseResponse,
    }))

    //this.fetchAarchives();

    // set uuid & fetch data ( all in one because of deep watching )
    this.aParams.uuid = this.tParams.uuid = this.aid
  },
  methods:
  {
    reIndexOf(array, rx)
    {
      for (let i in array) if (array[i].toString().match(rx)) return i
      return -1
    },
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
