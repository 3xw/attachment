<!-- files -->
<script type="text/x-template" id="attachment-files-index">
  <div id="attachment-files-index" data-intro="Liste des vos médias." data-position="top">
      <?= __d('Attachment','Edit') ?>
    </button>
    <div class="row" >
      <div v-for="(file, index) in files" :id="index"  class="attachment-files__item col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="card mb-4" >

          <!-- thumb -->
          <attachment-thumb :url="settings.url" :file="file"></attachment-thumb>

          <div class="card-body">

            <!-- infos -->
            <p class="card-text small">
              {{file.name | truncate(15) }}<br/>
              {{file.size | bytesToMegaBytes | decimal(2) }} MB<br/>
            </p>

            <!-- buttons -->
            <div class="btn-group">
              <a class="btn btn-fill btn-xs btn-success" target="_blank" role="button" v-bind:href="file.download_link" v-if="settings.actions.indexOf('download') != -1">
                <?= __d('Attachment','Download') ?>
              </a>
              <button class="btn btn-fill btn-xs btn-success" role="button" v-on:click="dispatch('show-view-file',index)" v-if="settings.actions.indexOf('view') != -1">
                <?= __d('Attachment','View') ?>
              </button>
              <button class="btn btn-fill btn-xs btn-info" role="button" v-on:click="dispatch('show-edit-file',index)" v-if="settings.actions.indexOf('edit') != -1">
                <?= __d('Attachment','Edit') ?>
              </button>
              <button class="btn btn-fill btn-xs btn-danger" role="button" v-on:click="dispatch('delete-file',index)" v-if="settings.actions.indexOf('delete') != -1">
                <?= __d('Attachment','Delete') ?>
              </button>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</script>
