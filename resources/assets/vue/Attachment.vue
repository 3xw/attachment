<template>
  <div :is="(mode == 'thumbInfo')? 'tbody' : 'div'" @mouseover="hover = true" @mouseleave="hover = false">
    <div v-if="mode == 'mosaic' && $options.filters.isThumbable(attachment)" class="attachment-thumb">
      <img @click="toggleFile(attachment)" v-if="$options.filters.isThumbable(attachment)" v-bind:src="settings.baseUrls[settings.profile]+'thumbnails/'+attachment.profile+'/w678q90/'+attachment.path+'?'+attachment.thumb_params" class="img-fluid"  />
      <!--<button type="button" name="button" @click="toggleFile(attachment.id)">{{isSelected(attachment.id)? '-' : '+'}}</button>-->
      <div class="attachment-thumb__hover">
        <div v-if="hover && !isSelected(attachment.id)" class="d-flex flex-column justify-content-center align-items-center" >
          ajouter à la sélection
        </div>
        <div v-else-if="hover && isSelected(attachment.id)" class="d-flex flex-column justify-content-center align-items-center">
          <div class="badge">
            supprimer de la sélection
          </div>
          <div class="utils--spacer-mini"></div>
          <s>fichier selectionné</s>
        </div>
        <div v-else-if="!hover && isSelected(attachment.id)" class="d-flex flex-column justify-content-center align-items-center">
          <icon-check></icon-check>
          <div class="utils--spacer-mini"></div>
          fichier selectionné
        </div>
      </div>
    </div>
    <div v-else-if="mode == 'thumb'">
      <div @click="toggleFile(attachment)" class="card attachment-thumb">

        <!-- thumb -->
        <div class="attachment-thumb__icon-container" >
          <div>
            <img v-if="$options.filters.isThumbable(attachment)" v-bind:src="settings.baseUrls[settings.profile]+'thumbnails/'+attachment.profile+'/w678c4-3q90/'+attachment.path+'?'+attachment.thumb_params" class="card-img-top" />
            <span v-html="$options.filters.icon(attachment.type+'/'+attachment.subtype)"></span>
            <!-- overlay -->
            <div class="attachment-thumb__hover">
              <div v-if="hover && !isSelected(attachment.id)" class="d-flex flex-column justify-content-center align-items-center" >
                ajouter à la sélection
              </div>
              <div v-else-if="hover && isSelected(attachment.id)" class="d-flex flex-column justify-content-center align-items-center">
                <div class="badge">
                  supprimer de la sélection
                </div>
                <div class="utils--spacer-mini"></div>
                <s>fichier selectionné</s>
              </div>
              <div v-else-if="!hover && isSelected(attachment.id)" class="d-flex flex-column justify-content-center align-items-center">
                <icon-check></icon-check>
                <div class="utils--spacer-mini"></div>
                fichier selectionné
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
            <img v-if="$options.filters.isThumbable(attachment)" v-bind:src="settings.baseUrls[settings.profile]+'thumbnails/'+attachment.profile+'/w60c1-1q75/'+attachment.path+'?'+attachment.thumb_params" width="60" class="card-img-top" />
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
        <div class="btn-group">
          <a :href="attachment.url"><span class="glyphicon glyphicon-"></span></a>
        </div>
        <div @click="toggleFile(attachment)" class="btn color--white " :class="isSelected(attachment.id )? 'btn--blue' : 'btn--blue-light'">
          <span v-if="!isSelected(attachment.id)">Ajouter à la sélection</span>
          <span v-else><icon-check></icon-check>  selectionné</span>
        </div>
      </td>
    </tr>
  </div>
</template>
<script>
import iconCheck from './icons/check.vue'

export default
{
  name:'attachment',
  components: {
    'icon-check': iconCheck
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
    }
  }
}
</script>
