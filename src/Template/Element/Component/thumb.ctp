<!-- thumb -->
<script type="text/x-template" id="attachment-thumb">
  <img v-if="file | isThumbable" v-bind:src="url+'thumbnails/'+file.profile+'/w678c16-9/'+file.path" />
  <div class="attachment-thumb__icon-container" v-if="file | isNotThumbable" >
    <div>{{{file.type+'/'+file.subtype | icon }}}</div>
  </div>
</script>
