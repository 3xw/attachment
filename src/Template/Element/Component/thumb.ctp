<!-- thumb -->
<script type="text/x-template" id="attachment-thumb">
  <div class="attachment-thumb__icon-container" >
    <div>
      <img v-if="file | isThumbable" v-bind:src="url+'thumbnails/'+file.profile+'/w678c16-9/'+file.path" class="img-responsive" />
      {{{file.type+'/'+file.subtype | icon }}}
    </div>
  </div>
</script>
