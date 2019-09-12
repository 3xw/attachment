<template>
  <section class="section-attachment--index">
    <div class="section__header d-flex flex-row justify-content-between align-items-center">
      <h3 class="mb-0">{{attachments.length}} Fichiers</h3>
      <div class="section__filter d-flex flex-row">
        <button type="button" @click="mode = 'mosaic'" name="button" class="btn btn-secondary mb-0 color--white" :class="{active: mode == 'mosaic'}"><i class="material-icons">view_quilt</i></button>
        <button type="button" @click="mode = 'thumb'" name="button" class="btn btn-secondary mb-0 color--white" :class="{active: mode == 'thumb'}"><i class="material-icons">view_module</i></button>
        <button type="button" @click="mode = 'thumbInfo'" name="button" class="btn btn-secondary mb-0 color--white" :class="{active: mode == 'thumbInfo'}"><i class="material-icons">view_list</i></button>
        &nbsp;
        <button type="button" v-if="selectedFiles.length > 0" @click="dowloadSelection" name="button" class="btn btn-primary mb-0 color--white">TÉLÉCHARGER {{selectedFiles.length}} FICHIER(S)</button>
      </div>
    </div>
    <div class="utils--spacer-semi"></div>
    <div v-if="aParams.atags || aParams.filters" class="f-flex flex-row">
      <p class="small color--grey d-inline-block">Filtre(s): </p>
      <span v-if="aParams.atags" class="badge badge-secondary" @click="removeAtag(atag)" :key="atag" v-for="atag in aParams.atags.split(',')">{{atag}} <i class="material-icons">close</i></span>
      <span v-if="aParams.filters" class="badge badge-secondary" @click="removeFilter(filter)" :key="atag" v-for="filter in aParams.filters.split(',')">{{filter}} <i class="material-icons">close</i></span>
      <div class="utils--spacer-semi"></div>
    </div>
    <div class="section__index" v-if="attachments">
      <transition name="fade">
        <div v-if="mode == 'mosaic'" v-images-loaded="imgReady">
          <div  v-packery='{itemSelector: ".packery-item", percentPosition: true}' id="mosaic" class="row packery-row">
            <div v-for="(attachment, i ) in attachments" :key="i" v-packery-item class="packery-item col-lg-4 col-md-6">
              <attachment :index="i" :aid="aid" :mode="mode" :attachment="attachment"></attachment>
            </div>
          </div>
        </div>
        <div v-else-if="mode == 'thumb'">
          <div>
            <div class="row">
              <div v-for="(attachment, i ) in attachments" :key="i" class="col-sm-6 col-md-4 col-xl-3">
                <attachment :index="i" :aid="aid" :mode="mode" :attachment="attachment"></attachment>
              </div>
            </div>
          </div>
        </div>
        <div v-else-if="mode == 'thumbInfo'">
          <table class="table w-100">
            <attachment v-for="(attachment, i ) in attachments" :index="i" :aid="aid" :mode="mode" :attachment="attachment"></attachment>
          </table>
        </div>
      </transition>
    </div>
    <div class="clearfix"></div>
    <div class="utils--spacer-semi"></div>
    <div v-if="pagination">
      <attachment-pagination :aid="aid" :pagination="pagination" :settings="settings"></attachment-pagination>
    </div>
  </section>
</template>

<script>
import { packeryEvents } from 'vue-packery-plugin'
import Attachment from './Attachment.vue'
import Pagination from './Pagination.vue'
import imagesLoaded from 'vue-images-loaded'

export default
{
  name:'attachments',
  directives: {
    imagesLoaded
  },
  props: { aid: String, settings: Object },
  data(){
    return {
      mode: 'mosaic'
    }
  },
  components:
  {
    'attachment': Attachment,
    'attachment-pagination': Pagination
  },
  computed:
  {
    aParams()
    {
      return this.$store.get(this.aid + '/aParams')
    },
    attachments()
    {
      return this.$store.get(this.aid + '/attachments/list')
    },
    pagination()
    {
      return this.$store.get(this.aid + '/pagination')
    },
    selectedFiles()
    {
      return this.$store.get(this.aid + '/selection.files')
    }
  },
  watch: {
    mode: function(){
      if(this.mode == 'mosaic'){
        this.reLayout()
      }else{
      }
    }
  },
  methods:
  {
    removeAtag(atag)
    {
      var list = this.aParams.atags.split(',');
      for(var i = 0 ; i < list.length ; i++) {
        if(list[i] == atag) list.splice(i, 1)
      }
      this.$store.set(this.aid + '/aParams', Object.assign(this.$store.get(this.aid + '/aParams'),{ atags: list.join(','), page: 1 }))
    },
    removeFilter(filter)
    {
      var list = this.aParams.filters.split(',');
      for(var i = 0 ; i < list.length ; i++) {
        if(list[i] == filter) list.splice(i, 1)
      }
      this.$store.set(this.aid + '/aParams', Object.assign(this.$store.get(this.aid + '/aParams'),{ filters: list.join(','), page: 1 }))
    },
    reLayout()
    {
      setTimeout(function(){
        var grid = document.getElementById('mosaic')
        packeryEvents.$emit('layout', grid)
      }, 200)
    },
    imgReady()
    {
      this.reLayout()
    },
    dowloadSelection()
    {
      let token = this.$store.get(this.aid+'/selection.token')
      let url = this.$store.get(this.aid + '/settings.url')+'attachment/download/files/'+token
      window.open(url)
    }
  },
  mounted()
  {
    this.reLayout()
  }
}
</script>
