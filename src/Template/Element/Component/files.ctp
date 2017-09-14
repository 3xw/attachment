<!-- files -->
<script type="text/x-template" id="attachment-files">
  <div id="attachment-files" data-intro="Liste des médias séléctionnés, vous pouvez les ordonner en les déplaçant à la souris." data-position="left">

    <!-- add Array input -->
    <input v-if="settings.relation == 'belongsToMany'" type="hidden" name="attachments[]" value="" id="AttachmentAttachment_">

    <!-- BelongsTo default value -->
    <input v-if="settings.relation != 'belongsToMany'" type="hidden" :name="settings.field" value="0">

    <div class="row" v-sortable="{draggable:'.attachment-files__item', onEnd:order}" >
      <div v-for="(file, index) in files" :id="file.id"  class="attachment-files__item" v-bind:class="settings.cols">
        <div class="thumbnail" >

          <!-- thumb -->
          <attachment-thumb :url="settings.url" :file="file"></attachment-thumb>

          <div class="caption">
              {{file.name | truncate(15) }}<br/>
              {{file.size | bytesToMegaBytes | decimal(2) }} MB<br/>
            <!-- data -->
            <input v-if="settings.relation == 'belongsToMany'" type="hidden" :name="'attachments['+index+'][id]'" :value="file.id">
            <input v-if="settings.relation == 'belongsToMany'" type="hidden" :name="'attachments['+index+'][_joinData][order]'" :value="index">

            <input v-if="settings.relation != 'belongsToMany'" type="hidden" :name="settings.field" :value="file.id">


            <button class="btn btn-fill btn-xs btn-warning" v-on:click.prevent="remove(file.id)" >Enlever</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</script>
