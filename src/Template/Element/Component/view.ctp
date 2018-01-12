<!-- upload -->
<script type="text/x-template" id="attachment-view">
  <div id="attachment-view" class="modal-mask" v-if="show" transition="modal">
    <div class="modal-wrapper">
      <div class="modal-container container" style="max-height: 600px; scroll:auto;">
        <div class="custom-modal-header">
          <h4 v-html="$options.filters.icon(file.type+'/'+file.subtype)"></h4>
        </div>
        <div class="custom-modal-body" >

          <div>

            <!-- url -->
            <p v-if="$options.filters.isNotEmbed(file)">
              <?= __d('Attachment','Url') ?>: {{settings.baseUrls[file.profile]}}{{file.path}}
            </p>

            <!-- url -->
            <p>
              <?= __d('Attachment','Type') ?>: {{file.type+'/'+file.subtype}}
            </p>

            <!-- embed -->
            <div v-if="$options.filters.isEmbed(file)" v-html="file.embed"></div>

            <!-- thumb -->
            <img v-if="$options.filters.isNiceImage(file)" v-bind:src="settings.url+'thumbnails/'+file.profile+'/w1200/'+file.path" class="img-fluid" />
          </div>

        </div>
        <p></p>
        <div class="custom-modal-footer">
          <div class="btn-group">
            <button type="button" class="modal-default-button btn btn-warning" @click="close()">
              <?= __d('Attachment','Close') ?>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</script>
