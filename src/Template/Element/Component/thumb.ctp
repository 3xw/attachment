<!-- thumb -->
<script type="text/x-template" id="attachment-thumb">
  <div class="attachment-thumb__icon-container" >
    <div>
      <img v-if="$options.filters.isThumbable(file)" v-bind:src="url+file.profile+'/w678c16-9/'+file.path" class="card-img-top" />
      <span v-html="$options.filters.icon(file.type+'/'+file.subtype)"></span>
    </div>
  </div>
</script>
