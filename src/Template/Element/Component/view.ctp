<!-- upload -->
<script type="text/x-template" id="attachment-view">
  <div id="attachment-view" class="modal-mask" v-if="show" transition="modal">
    <div class="modal-wrapper">
      <div class="modal-container container" style="max-height: 600px; scroll:auto;">
        <div class="custom-modal-header">
          <h4>{{{file.type+'/'+file.subtype | icon }}} - {{file.name}}</h4>
        </div>
        <div class="custom-modal-body" >

          <div>

            <!-- url -->
            <p v-if="file | isNotEmbed">
              <?= __('Url') ?>: {{settings.baseUrls[file.profile]}}{{file.path}}
            </p>

            <!-- url -->
            <p>
              <?= __('Type') ?>: {{file.type+'/'+file.subtype}}
            </p>

            <!-- embed -->
            <div v-if="file | isEmbed">
              {{file.embed}}
            </div>

            <!-- thumb -->
            <img v-if="file | isNiceImage" v-bind:src="settings.url+'thumbnails/'+file.profile+'/w1200/'+file.path" class="img-responsive" />
          </div>

        </div>
        <p></p>
        <div class="custom-modal-footer">
          <div class="btn-group">
            <button type="button" class="modal-default-button btn btn-warning" @click="close()">
              Fermer
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</script>
