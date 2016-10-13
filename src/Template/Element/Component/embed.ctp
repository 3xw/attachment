<!-- embed -->
<script type="text/x-template" id="attachment-embed">
  <div id="attachment-embed" class="modal-mask" v-if="show" transition="modal">
    <div class="modal-wrapper">
      <div class="modal-container container">
        <div class="custom-modal-header">
          <?= __('Ajouter un embed code') ?>
        </div>
        <div class="custom-modal-body">

          <!-- WARNINGS -->
          <div v-for="(index, error) in errors" track-by="$index" class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close"  aria-label="Close" @click="errors = []" ><span aria-hidden="true">&times;</span></button>
            <strong>Attention!</strong> {{error}}
          </div>

          <!-- TAGS -->
          <div v-if="this.settings.restrictions.indexOf('tag_restricted') == -1" id="attachment-atags">
            <label >Tags</label>
            <select id="atagsinput" multiple class="form-control">
              <option v-for="(index, atag) in atags" value="{{atag}}">{{atag}}</option>
            </select>
          </div>

          <!-- INPUTS -->
          <div v-if="show">

            <!-- OPTIONS -->
            <div  id="attachment-options">
              <div class="row optional-fields">
                <div class="col-md-6">
                  <div class="input text">
                    <label for="title">Title</label>
                    <input type="text" name="title" class="form-control" id="title">
                  </div>
                  <div class="input text">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" id="description" rows="5"></textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input text">
                    <label for="author">Author</label>
                    <input type="text" name="author" class="form-control" id="author">
                  </div>
                  <div class="input text">
                    <label for="copyright">Copyright</label>
                    <input type="text" name="copyright" class="form-control" id="copyright">
                  </div>
                </div>
              </div>
            </div>

            <!-- Name HERE -->
            <div class="input text required">
              <label for="name"><?= __('Name') ?></label>
              <input type="text" name="name" class="form-control attachment-embed__name" id="name" />
            </div>

            <!-- EMBED CODE HERE -->
            <div class="input text required">
              <label for="embed"><?= __('Embed code') ?></label>
              <textarea name="embed" class="form-control attachment-embed__embed" id="embed" rows="5"></textarea>
            </div>

        </div>
        <p></p>
        <div class="custom-modal-footer">
          <div class="btn-group">
            <button type="button" class="modal-default-button btn btn-success" @click="upload">
              Téléversser
            </button>
            <button type="button" class="modal-default-button btn btn-warning" @click="close()">
              Fermer
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</script>
