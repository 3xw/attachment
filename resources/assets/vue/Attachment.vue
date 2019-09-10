<template>

  <div class="card mb-4" >

    <!-- thumb -->
    <div class="attachment-thumb__icon-container" >
      <div>
        <img v-if="$options.filters.isThumbable(attachment)" v-bind:src="url+'thumbnails/'+attachment.proattachment+'/w678c16-9/'+attachment.path" class="card-img-top" />
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

</template>

<script>
export default
{
  name:'attachment',
  props:
  {
    attachment: Object
  },
  data: function()
  {
    return {
      url: process.env.PUBLIC_PATH
    }
  }
}
</script>
