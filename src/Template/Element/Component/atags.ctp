<!-- files -->
<script type="text/x-template" id="attachment-atags">
  <div id="attachment-atags" v-if="settings.atagsDisplay != false">
    <label><?= __d('Attachment','Tags') ?></label>
    <select name="atags" id="atagsinput" multiple class="form-control">
      <option :selected="(activeTags.indexOf(atag) !== -1)? 'selected' : false" v-for="atag in atags" :value="atag">
        {{atag}}
      </option>
    </select>
  </div>
</script>
