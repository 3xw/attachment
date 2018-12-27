<!-- files -->
<script type="text/x-template" id="attachment-atags">
  <div id="attachment-atags" v-if="settings.restrictions.indexOf('tag_restricted') == -1 && settings.restrictions.indexOf('tag_or_restricted') == -1">
    <label ><?= __d('Attachment','Tags') ?></label>
    <select name="atags" id="atagsinput" multiple class="form-control">
      <option
        v-for="atag in file.atags"
        :value="atag.name"
        selected="selected">
          {{atag.name}}
      </option>
    </select>
  </div>
</script>
