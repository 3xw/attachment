<template>
  <div :is="(mode == 'thumbInfo')? 'tbody' : 'div'">
    <div v-if="mode == 'mosaic' && $options.filters.isThumbable(attachment)">
      <img v-if="$options.filters.isThumbable(attachment)" v-bind:src="settings.url+'thumbnails/'+attachment.profile+'/w678q90/'+attachment.path" class="img-fluid"  />
      <button type="button" name="button" @click="toggleFile(attachment.id)">{{isSelected(attachment.id)? '-' : '+'}}</button>
    </div>
    <div v-else-if="mode == 'thumb'">
      <div class="card mb-4" >
        <!-- thumb -->
        <div class="attachment-thumb__icon-container" >
          <div>
            <img v-if="$options.filters.isThumbable(attachment)" v-bind:src="settings.url+'thumbnails/'+attachment.profile+'/w678c16-9/'+attachment.path" class="card-img-top" />
            <span v-html="$options.filters.icon(attachment.type+'/'+attachment.subtype)"></span>
          </div>
        </div>
        <div class="card-body">
            <p class="card-text small">
              <span v-if="attachment.title">{{attachment.title | truncate(15) }}<br/></span>
              {{attachment.name | truncate(15) }}<br/>
              {{attachment.size | bytesToMegaBytes | decimal(2) }} MB<br/>
            </p>
          <!-- data -->
          <input v-if="settings.relation == 'belongsToMany'" type="hidden" :name="'attachments['+index+'][id]'" :value="attachment.id">
          <input v-if="settings.relation == 'belongsToMany'" type="hidden" :name="'attachments['+index+'][_joinData][order]'" :value="index">
          <input v-if="settings.relation != 'belongsToMany'" type="hidden" :name="settings.field" :value="attachment.id">
          <button class="btn btn-fill btn-xs btn-warning" v-on:click.prevent="remove(attachment.id)" >Remove</button>
        </div>
      </div>
    </div>
    <tr v-else-if="mode == 'thumbInfo'">
      <td>
        <img v-if="$options.filters.isThumbable(attachment)" v-bind:src="settings.url+'thumbnails/'+attachment.profile+'/w60c1-1q75/'+attachment.path" />
      </td>
      <td>
        <span v-if="attachment.title">{{attachment.title}} - </span>{{attachment.name}} - {{attachment.size | bytesToMegaBytes | decimal(2) }} MB
      </td>
    </tr>
  </div>
</template>

<script>
export default
{
  name:'attachment',
  props:{attachment: Object,index: Number,aid: String, mode: String},
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
    toggleFile(id)
    {
      if(this.selectedFiles.indexOf(id) == -1){
        this.$store.commit(this.aid+'/addFileToSelection', id)
      }else{
        this.$store.commit(this.aid+'/removeFileFromSelection', id)
      }
    },
    isSelected(id)
    {
      return (this.selectedFiles.indexOf(id) != -1)
    }
  }
}
</script>
