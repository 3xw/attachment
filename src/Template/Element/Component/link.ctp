<!-- link -->
<script type="text/x-template" id="attachment-link">
  <div id="attachment-link" class="modal-mask" v-if="show" transition="modal">
    <div class="modal-wrapper">
      <div class="modal-container container">
        <div class="custom-modal-header">
          <?= __d('Attachment','Add a link') ?>
        </div>
        <div class="custom-modal-body">

          <!-- WARNINGS -->
          <div v-for="(error, index) in errors" track-by="$index" class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close" aria-label="Close" @click="errors = []"><span aria-hidden="true">&times;</span></button>
            <strong><?= __d('Attachment','Watch out') ?></strong> {{error}}
          </div>

          <!-- TAGS -->
          <attachment-atags :aid="aid" :settings="settings"></attachment-atags>

          <!-- INPUTS -->
          <div v-if="show">
            <div id="attachment-options">
              <div class="row optional-fields">
                <div class="col-md-6">
                  <div class="input text">
                    <label for="title"><?= __d('Attachment','Title') ?></label>
                    <input type="text" name="title" class="form-control" id="title">
                  </div>
                  <div class="input text">
                    <label for="description"><?= __d('Attachment','Description') ?></label>
                    <textarea name="description" class="form-control" id="description" rows="5"></textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input text">
                    <label for="author"><?= __d('Attachment','Author') ?></label>
                    <input type="text" name="author" class="form-control" id="author">
                  </div>
                  <div class="input text">
                    <label for="copyright"><?= __d('Attachment','Copyright') ?></label>
                    <input type="text" name="copyright" class="form-control" id="copyright">
                  </div>
                </div>
              </div>
            </div>

            <!-- Name HERE -->
            <div class="input text required">
              <label for="name"><?= __d('Attachment','Name') ?></label>
              <input type="text" name="name" class="form-control attachment-link__name" id="name" />
            </div>

            <!-- URL HERE -->
            <div class="input text required">
              <label for="link"><?= __d('Attachment','URL') ?></label>
              <input type="url" name="link" class="form-control attachment-link__url" id="link" placeholder="https://..." />
            </div>
          </div>
          <p></p>
          <div class="custom-modal-footer">
            <div class="btn-group">
              <button type="button" class="modal-default-button btn btn-success" @click="upload">
                <?= __d('Attachment','Add') ?>
              </button>
              <button type="button" class="modal-default-button btn btn-warning" @click="close()">
                <?= __d('Attachment','Close') ?>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</script>
