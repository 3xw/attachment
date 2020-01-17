<template>
  <div :is="(mode == 'thumbInfo')? 'tbody' : 'div'" @mouseover="hover = true" @mouseleave="hover = false">
    <div v-if="mode == 'mosaic' && $options.filters.isThumbable(attachment)" class="attachment-thumb">
      <img v-if="$options.filters.isThumbable(attachment)" v-bind:src="settings.baseUrls['cdnThumbnails']+attachment.profile+'/w678q90/'+attachment.path+'?'+attachment.thumb_params" class="img-fluid"  />
      <div class="attachment-thumb__hover">
        <div v-if="isSelected(attachment.id)" class="d-flex flex-column justify-content-center align-items-center">
          <icon-check></icon-check>
          <div class="utils--spacer-mini"></div>
          fichier selectionné
        </div>
        <div v-if="hover" class="attachment-thumb__actions d-flex flex-column justify-content-end align-items-end">
          <div class="btn-group">
            <div title="Aperçu" alt="Aperçu" class="btn btn--green color--white" @click="preview(attachment)"><i class="material-icons"> remove_red_eye </i></div>
            <div title="Télécharger" alt="Télécharger" class="btn btn--blue color--white" @click="downloadFile(attachment)"><i class="material-icons"> cloud_download </i></div>
            <div title="Ajouter à la séléction" alt="Ajouter à la séléction" class="btn btn--blue-dark color--white" @click="toggleFile(attachment)">
              <i v-if="!isSelected(attachment.id)" class="material-icons"> add_circle </i>
              <i v-else class="material-icons"> remove_circle </i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-else-if="mode == 'thumb'">
      <div class="card attachment-thumb">
        <div class="attachment-thumb__icon-container" >
          <div>
            <img v-if="$options.filters.isThumbable(attachment)" v-bind:src="settings.baseUrls['cdnThumbnails']+attachment.profile+'/w678c4-3q90/'+attachment.path+'?'+attachment.thumb_params" class="card-img-top" />
            <span v-html="$options.filters.icon(attachment.type+'/'+attachment.subtype)"></span>
            <!-- overlay -->
            <div class="attachment-thumb__hover">
              <div v-if="isSelected(attachment.id)" class="d-flex flex-column justify-content-center align-items-center">
                <icon-check></icon-check>
                <div class="utils--spacer-mini"></div>
                fichier selectionné
              </div>
              <div v-if="hover" class="attachment-thumb__actions d-flex flex-column justify-content-end align-items-end">
                <div class="btn-group">

                  <!-- VIEW -->
                  <div
                  v-if="settings.actions.indexOf('view') != -1"
                  @click="preview(attachment)"
                  title="Aperçu" alt="Aperçu" class="btn btn--green color--white">
                    <i class="material-icons"> remove_red_eye </i>
                  </div>

                  <!-- DOWNLOAD -->
                  <div
                  v-if="settings.actions.indexOf('download') != -1"
                  @click="downloadFile(attachment)"
                  title="Télécharger" alt="Télécharger" class="btn btn--blue color--white">
                    <i class="material-icons"> cloud_download </i>
                  </div>

                  <!-- SELECT -->
                  <div
                  v-if="settings.groupActions.length > 0"
                  @click="toggleFile(attachment)"
                  title="Ajouter à la sélection" alt="Ajouter à la sélection" class="btn btn--blue-dark color--white" >
                    <i v-if="!isSelected(attachment.id)" class="material-icons"> add_circle </i>
                    <i v-else class="material-icons"> remove_circle </i>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-body">
            <p class="card-text small">
              <span v-if="attachment.title">{{attachment.title}}<br/></span>
              {{attachment.name}}<br/>
              <!--{{attachment.size | bytesToMegaBytes | decimal(2) }} MB<br/>-->
            </p>
          <!-- data -->
          <!--<input v-if="settings.relation == 'belongsToMany'" type="hidden" :name="'attachments['+index+'][id]'" :value="attachment.id">
          <input v-if="settings.relation == 'belongsToMany'" type="hidden" :name="'attachments['+index+'][_joinData][order]'" :value="index">
          <input v-if="settings.relation != 'belongsToMany'" type="hidden" :name="settings.field" :value="attachment.id">-->
          <!--<button class="btn btn-fill btn-xs btn-warning" v-on:click.prevent="remove(attachment.id)" >Remove</button>-->
        </div>
      </div>
    </div>
    <tr v-else-if="mode == 'thumbInfo'">
      <td>
        <div class="attachment-thumb__icon-container table" >
          <div>
            <img v-if="$options.filters.isThumbable(attachment)" v-bind:src="settings.baseUrls['cdnThumbnails']+attachment.profile+'/w60c1-1q75/'+attachment.path+'?'+attachment.thumb_params" width="60" class="card-img-top" />
            <span v-html="$options.filters.icon(attachment.type+'/'+attachment.subtype)"></span>
          </div>
        </div>
      </td>
      <td>
        <span v-if="attachment.title">{{attachment.title}} | </span>
        {{attachment.name}}<br>
        {{attachment.date}}<br>
        {{attachment.size | bytesToMegaBytes | decimal(2) }} MB
      </td>
      <td class="text-right">
        <div class="btn-group attachment__actions">
          <div title="Aperçu" alt="Aperçu" class="btn btn--green color--white" @click="preview(attachment)"><i class="material-icons"> remove_red_eye </i></div>
          <div title="Télécharger" alt="Télécharger" class="btn btn--blue color--white" @click="downloadFile(attachment)"><i class="material-icons"> cloud_download </i></div>
          <div title="Ajouter à la sélection" alt="Ajouter à la sélection" class="btn btn--blue-dark color--white" @click="toggleFile(attachment)">
            <i v-if="!isSelected(attachment.id)" class="material-icons"> add_circle </i>
            <i v-else class="material-icons"> remove_circle </i>
          </div>
        </div>
      </td>
    </tr>
  </div>
</template>
<script>
import { client } from '../js/client.js'

import iconCheck from './icons/check.vue'


export default
{
  name:'attachment',
  components: {
    'icon-check': iconCheck,
  },
  props:{attachment: Object, settings: Object, index: Number, aid: String, mode: String},
  data()
  {
    return {
      hover: false
    }
  },
  computed:
  {
    settings()
    {
      return this.$store.get(this.aid+'/settings')
    },
    selectedFiles()
    {
      return this.$store.get(this.aid + '/selection.files')
    }
  },
  methods: {
    toggleFile(attachment)
    {
      if(this.selectedFiles.findIndex(f => f.id === attachment.id) == -1){
        this.$store.commit(this.aid+'/addFileToSelection', attachment)
      }else{
        this.$store.commit(this.aid+'/removeFileFromSelection', attachment)
      }
    },
    isSelected(id)
    {
      return (this.selectedFiles.findIndex(f => f.id === id) !== -1)
    },
    forceFileDownload(response, attachment){
      const url = window.URL.createObjectURL(new Blob([response.data], { type: response.headers['content-type'] }))
      const link = document.createElement('a')
      link.href = url
      link.setAttribute('download', attachment.name) //or any other extension
      document.body.appendChild(link)
      link.click()
    },
    downloadFile(attachment){
      client.get(attachment.url, {responseType: 'arraybuffer'})
      .then(response => {
        this.forceFileDownload(response, attachment)
      })
      .catch(() => console.log('error occured'))
    },
    preview(attachment){
      this.$store.set(this.aid + '/preview', attachment)
      this.$forceUpdate()
    }
  }
}
</script>
