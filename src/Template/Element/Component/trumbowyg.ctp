<!-- input -->
<script type="text/x-template" id="attachment-trumbowyg">
  <div class="input form-group">
    <label>{{settings.label}}</label>
    <div class="attachment-trumbowyg">

      <!-- upload -->
      <attachment-upload :settings.sync="settings" ></attachment-upload>

      <!-- browse -->
      <attachment-browse :types="types" :tags="tags" :settings="settings" ></attachment-browse>

      <!-- embed -->
      <attachment-embed :settings.sync="settings" ></attachment-embed>

      <!-- trumbowyg -->
      <div v-bind:class="settings.uuid">{{settings.content}}</div>

    </div>
  </div>
</script>
