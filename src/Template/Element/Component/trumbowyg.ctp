<!-- input -->
<script type="text/x-template" id="attachment-trumbowyg">
  <div class="input form-group" >
    <label>{{settings.label}}</label>
    <div class="attachment-trumbowyg" :class="settings.class">

      <!-- upload -->
      <attachment-upload :aid="aid" :settings.sync="settings" ></attachment-upload>

      <!-- browse -->
      <attachment-browse :aid="aid" :types="types" :tags="tags" :settings="settings" :from="'trumbowyg'"></attachment-browse>

      <!-- trumbowyg-options -->
      <attachment-trumbowyg-options :aid="aid" :settings.sync="settings" :file.sync="file" ></attachment-trumbowyg-options>

      <!-- trumbowyg -->
      <textarea :aid="aid" :name="settings.field" v-bind:class="settings.uuid">{{settings.content}}</textarea>

    </div>
  </div>
</script>
