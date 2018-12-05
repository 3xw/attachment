<!-- browse -->
<script type="text/x-template" id="attachment-inline-options">
  <div id="attachment-trumbowyg-options" class="modal-mask" v-if="show" transition="modal">
    <div class="modal-wrapper">
      <div class="modal-container container">
        <div class="custom-modal-header">
          <?= __d('Attachment','Choose options') ?>
        </div>
        <div class="custom-modal-body">

          <div class="row">

            <!-- col 1 -->
            <div class="col-md-6">
              <div class="thumbnail" >
                <!-- thumb -->
                <attachment-thumb :url="settings.url" :file="file"></attachment-thumb>

                <div class="caption">
                  {{file.name | truncate(100) }}<br/>
                  {{file.size | bytesToMegaBytes | decimal(2) }} MB<br/>
                </div>
              </div>
            </div>

            <!-- ACTION -->
            <div class="col-md-6">
              <select v-model="selection.displayAs" class="form-control">
                <option v-for="(value, key) in options.displayAs" :value="value">{{value}}</option>
              </select>


              <!-- LINK -->
              <div v-if="selection.displayAs == 'Link'">
                <!-- link title -->
                <div class="input">
                  <label><?= __d('Attachment','Title') ?></label>
                  <input type="text" class="form-control" v-model="selection.title" placeholder="<?= __d('Attachment','Title') ?>">
                </div>
              </div>

              <!-- IMAGE -->
              <div v-if="selection.displayAs == 'Image'">
                <div class="row">

                  <div class="col-md-6">
                    <!-- align -->
                    <div class="input select">
                      <label><?= __d('Attachment','Align') ?></label>
                      <select v-model="selection.align" class="form-control">
                        <option v-for="(value, key) in options.align" :value="value">{{value}}</option>
                      </select>
                    </div>
                    <!-- classes -->
                    <div class="input">
                      <label><?= __d('Attachment','Extra styles') ?></label>
                      <input type="text" class="form-control" v-model="selection.classes" placeholder="<?= __d('Attachment','classes') ?>">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <!-- width -->
                    <div class="input">
                      <label><?= __d('Attachment','Width') ?></label>
                      <input type="number" class="form-control" v-model="selection.width" min="0" step="5" v-bind:max="(file.width < 1200)? file.width: 1200" v-bind:value="(file.width < 1200)? file.width: 1200">
                    </div>
                    <!-- crop -->
                    <div v-if="selection.width" class="input">
                      <label><?= __d('Attachment','Crop') ?></label>
                      <div class="clearfix"></div>
                      <input type="checkbox" class="" v-model="selection.crop" value="0">
                      <?= __d('Attachment','Yes / no') ?>
                    </div>
                    <!-- crop settings-->
                    <div v-if="selection.width && selection.crop" class="input">
                      <label><?= __d('Attachment','Ratio') ?></label>
                      <div class="clearfix"></div>
                      <input type="number" v-model="selection.cropWidth" min="1" step="1" max="32" value="16">:<input type="number" v-model="selection.cropHeight" min="1" step="1" max="32" value="9">
                    </div>
                  </div>

                </div>
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
