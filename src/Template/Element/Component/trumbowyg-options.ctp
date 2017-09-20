<!-- browse -->
<script type="text/x-template" id="attachment-trumbowyg-options">
  <div id="attachment-trumbowyg-options" class="modal-mask" v-if="show" transition="modal">
    <div class="modal-wrapper">
      <div class="modal-container container">
        <div class="custom-modal-header">
          <?= __('Choisir les options') ?>
        </div>
        <div class="custom-modal-body">

          <div class="row">

            <div class="col-sm-6">
              <div class="thumbnail" >
                <!-- thumb -->
                <attachment-thumb :url="settings.url" :file="file"></attachment-thumb>

                <div class="caption">
                  {{file.name | truncate 100 }}<br/>
                  {{file.size | bytesToMegaBytes | decimal 2 }} MB<br/>
                </div>
              </div>
            </div>

            <!-- col 1 -->
            <div class="col-md-3" v-if="settings.trumbowyg.imageOptions && file.type == 'image'">
              <!-- align -->
              <div v-if="settings.trumbowyg.imageOptions.align" class="input select">
                <label><?= __d('Attachment','Align') ?></label>
                <select v-model="options.align" class="form-control">
                  <option v-for="(value, key) in settings.trumbowyg.imageOptions.align" value="{{key}}">{{value}}</option>
                </select>
              </div>

              <!-- title -->
              <div v-if="settings.trumbowyg.imageOptions.altTitle" class="input">
                <label><?= __d('Attachment','Alternate title') ?></label>
                <input type="text" class="form-control" v-model="options.alt" placeholder="<?= __d('Attachment','alt here') ?>">
              </div>

              <!-- classes -->
              <div v-if="settings.trumbowyg.imageOptions.classes" class="input">
                <label><?= __d('Attachment','Extra styles') ?></label>
                <input type="text" class="form-control" v-model="options.classes" placeholder="<?= __d('Attachment','classes') ?>">
              </div>
            </div>

            <!-- col 2 -->
            <div class="col-md-3" v-if="settings.trumbowyg.imageOptions && file.type == 'image'" >

              <!-- width -->
              <div v-if="settings.trumbowyg.imageOptions.width" class="input">
                <label><?= __d('Attachment','Width') ?></label>
                <input type="number" class="form-control" v-model="options.width" min="0" step="5" v-bind:max="(file.width < 1200)? file.width: 1200" v-bind:value="(file.width < 1200)? file.width: 1200">
              </div>

              <!-- crop -->
              <div v-if="settings.trumbowyg.imageOptions.width" class="input">
                <label><?= __d('Attachment','Crop') ?></label>
                <div class="clearfix"></div>
                <input type="checkbox" class="" v-model="options.crop" value="settings.trumbowyg.imageOptions.crop">
                <?= __d('Attachment','Yes / no') ?>
              </div>

              <!-- crop settings-->
              <div v-if="settings.trumbowyg.imageOptions.width && options.crop" class="input">
                <label><?= __d('Attachment','Ratio') ?></label>
                <div class="clearfix"></div>
                <input type="number" v-model="options.cropWidth" min="1" step="1" max="32" value="16">:<input type="number" v-model="options.cropHeight" min="1" step="1" max="32" value="9">
              </div>

            </div>
          </div>

        </div>
        <p></p>
        <div class="custom-modal-footer">
          <div class="btn-group">
            <button type="button" class="modal-default-button btn btn-fill btn-warning" @click="close()">
              <?= __d('Attachment','Close') ?>
            </button>
            <button type="button" class="modal-default-button btn btn-fill btn-success" @click="success()">
              <?= __d('Attachment','Insert') ?>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</script>
