<!-- files -->
<script type="text/x-template" id="attachment-files">
  <div id="attachment-files">

    <!-- add Array input -->
    <input v-if="settings.relation == 'belongsToMany'" type="hidden" name="attachments[]" value="" id="AttachmentAttachment_">

    <div class="row" v-sortable="{draggable:'.attachment-files__item', onEnd:onEnd}" >
      <div v-for="(index, file) in settings.attachments" id="{{index}}"  class="attachment-files__item col-xs-4 col-md-3 col-lg-2">
        <div class="thumbnail" >

          <!-- thumb -->
          <attachment-thumb :url="settings.url" :file="file"></attachment-thumb>

          <div class="caption">
              {{file.name | truncate 15 }}<br/>
              {{file.size | bytesToMegaBytes | decimal 2 }} MB<br/>
            <!-- data -->
            <input v-if="settings.relation == 'belongsToMany'" type="hidden" name="attachments[{{index}}][id]" value="{{file.id}}">
            <input v-if="settings.relation == 'belongsToMany'" type="hidden" name="attachments[{{index}}][_joinData][order]" value="{{index}}">

            <input v-if="settings.relation != 'belongsToMany'" type="hidden" name="{{settings.field}}" value="{{file.id}}">


            <button class="btn btn-fill btn-xs btn-warning" role="button" @click="remove(index)" >Enlever</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</script>
