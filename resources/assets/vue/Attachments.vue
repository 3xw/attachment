<template>
  <section class="section-attachment--index">
    <div class="section__header">
      <attachment-search-bar :aid="aid"></attachment-search-bar>
      <div class="utils--spacer-semi"></div>
      <div class="d-flex flex-row justify-content-between align-items-center">
        <ul class="list-unstyled">
          <li>
            <ul class="list-unstyled list-inline" ><!-- v-if="types.isActive" -->
              <li class="text--upper list-inline-item pointer" v-for="(option, i2) in types.options" :key="i2" @click="types.current = option.slug;$forceUpdate();filterType(option.slug);"  :class="{active: types.current == option.slug}">
                <strong>{{option.name}}</strong>
              </li>
            </ul>
          </li>
        </ul>
        <div class="d-flex flex-row align-items-center">
          {{selectedFiles.length}} fichier(s) selectionné(s)&nbsp;&nbsp;
          <button type="button" v-if="selectedFiles.length > 0" @click="dowloadSelection" name="button" class="btn btn--blue-light mb-0 color--white">TÉLÉCHARGER</button>
        </div>
      </div>
      <div class="utils--spacer-semi"></div>
      <div class="d-flex flex-row justify-content-between align-items-center">
        <div>
          <div v-if="aParams.atags || aParams.filters" class="f-flex flex-row">
            <p class="small color--grey d-inline-block">Filtre(s): </p>
            <span v-if="aParams.atags" class="badge badge-secondary" @click="removeAtag(atag)" :key="atag" v-for="atag in aParams.atags.split(',')">{{atag}} <i class="material-icons">close</i></span>
            <span v-if="aParams.filters" class="badge badge-secondary" @click="removeFilter(filter)" :key="atag" v-for="filter in aParams.filters.split(',')">{{filter}} <i class="material-icons">close</i></span>
            <div class="utils--spacer-semi"></div>
          </div>
          <div class="section__sort d-flex flex-row align-items-center">
            <p class="small color--grey d-inline-block mb-0">Ordre: &nbsp;&nbsp;</p>
            <select v-model="sort" @change="changeOrder">
              <option value="created_desc">Plus récent en premier</option>
              <option value="created_asc">Plus ancien en premier</option>
            </select>
          </div>
        </div>
        <div class="section__filter d-flex flex-row">
          <button type="button" @click="mode = 'thumb'" name="button" class="btn btn--white mb-0" :class="{active: mode == 'thumb'}"><icon-grid></icon-grid></button>
          <button v-if="types.current == 'image' || types.current == 'video'" type="button" @click="mode = 'mosaic'" name="button" class="btn btn--white mb-0" :class="{active: mode == 'mosaic'}"><icon-mosaic></icon-mosaic></button>
          <button type="button" @click="mode = 'thumbInfo'" name="button" class="btn btn--white mb-0" :class="{active: mode == 'thumbInfo'}"><icon-list></icon-list></button>
        </div>
      </div>

    </div>

    <div class="section__index" v-if="attachments">
      <h3 class="text-right">
        <span v-if="pagination && pagination.count">{{pagination.count}}</span>
        <span v-else>0</span>
        Fichiers
      </h3>
      <div class="utils--spacer-mini"></div>
      <transition name="fade">
        <div v-if="mode == 'mosaic'" v-images-loaded="imgReady">
          <div  v-packery='{itemSelector: ".packery-item", percentPosition: true}' id="mosaic" class="row packery-row">
            <div v-for="(attachment, i ) in attachments" :key="i" v-packery-item class="packery-item col-lg-3 col-md-6">
              <attachment :index="i" :aid="aid" :mode="mode" :attachment="attachment"></attachment>
            </div>
          </div>
        </div>
        <div v-else-if="mode == 'thumb'">
          <div>
            <div class="row">
              <div v-for="(attachment, i ) in attachments" :key="i" class="col-sm-6 col-md-4 col-xl-2">
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
      <div class="clearfix"></div>
      <div class="utils--spacer-semi"></div>
      <div v-if="pagination">
        <attachment-pagination :aid="aid" :pagination="pagination" :settings="settings"></attachment-pagination>
      </div>
    </div>
  </section>
</template>
<script>
import { packeryEvents } from 'vue-packery-plugin'
import Attachment from './Attachment.vue'
import Pagination from './Pagination.vue'

import iconGrid from './icons/viewGrid.vue'
import iconMosaic from './icons/viewMosaic.vue'
import iconList from './icons/viewList.vue'

import SearchBar from './SearchBar.vue'

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
      sort: 'created_desc',
      mode: 'thumb',
      types: {
        name: 'Types',
        slug: 'type',
        isActive: false,
        current: 'image',
        options: [
          {
            name: 'Images',
            slug: 'image',
          },
          {
            name: 'Vidéos',
            slug: 'video',
          },
          {
            name: 'PDFS',
            slug: 'pdf',
          },
          {
            name: 'Autres',
            slug: 'application',
          }
        ]
      }
    }
  },
  components:
  {
    'attachment': Attachment,
    'attachment-pagination': Pagination,
    'attachment-search-bar': SearchBar,

    'icon-grid': iconGrid,
    'icon-mosaic': iconMosaic,
    'icon-list': iconList
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
    },

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
    filterType()
    {
      if(!this.upload){
        this.$store.set(this.aid + '/aParams', Object.assign(this.$store.get(this.aid + '/aParams'),{ type: this.types.current, filters: '', atags: '', page: 1 }))
      }
    },
    changeOrder()
    {
      this.$store.set(this.aid + '/aParams', Object.assign(this.$store.get(this.aid + '/aParams'),{ sort: this.sort, page: 1 }))
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
