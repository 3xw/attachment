<!-- upload -->
<script type="text/x-template" id="attachment-edit">
  <div id="attachment-edit" class="modal-mask" v-if="show" transition="modal">
    <div class="modal-wrapper">
      <div class="modal-container container">
        <div class="custom-modal-header">
          <?= __('Edit') ?>
        </div>
        <div class="custom-modal-body">

          <!-- WARNINGS -->
          <div v-for="(index, error) in errors" track-by="$index" class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close"  aria-label="Close" @click="errors = []" ><span aria-hidden="true">&times;</span></button>
            <strong>Attention!</strong> {{error}}
          </div>

          <!-- loading -->
          <div v-if="loading" class="attachment-loading-container">
            <img v-bind:src="this.settings.url+'attachment/img/loading.gif'" class="img-responsive" />
          </div>

          <!-- TAGS -->
          <div id="attachment-atags" v-if="this.settings.restrictions.indexOf('tag_restricted') == -1">
            <label >Tags</label>
            <select v-model="file.atags" name="atags" id="atagsinput" multiple class="form-control">
              <option
                v-for="(index, atag) in file.atags"
                value="{{atag.name}}"
                selected="selected">
                  {{atag.name}}
              </option>
            </select>
          </div>

          <!-- INPUTS -->
          <div v-if="show">

            <!-- IF EMBED -->
            <div v-if="file.type == 'embed'">
              <!-- Name HERE -->
              <div class="input text required">
                <label for="name"><?= __('Name') ?></label>
                <input v-model="file.name" type="text" name="name" class="form-control attachment-embed__name" id="name" />
              </div>

              <!-- EMBED CODE HERE -->
              <div class="input text required">
                <label for="embed"><?= __('Embed code') ?></label>
                <textarea v-model="file.embed" name="embed" class="form-control attachment-embed__embed" id="embed" rows="5"></textarea>
              </div>
            </div>

            <!-- OPTIONS -->
            <div  id="attachment-options">
              <div class="row optional-fields">
                <div class="col-md-6">
                  <div class="input text">
                    <label for="title">Title</label>
                    <input v-model="file.title" type="text" name="title" class="form-control" id="title">
                  </div>
                  <div class="input text">
                    <label for="title">Description</label>
                    <textarea v-model="file.description" name="description" class="form-control" id="description" rows="5"></textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input text">
                    <label for="author">Author</label>
                    <input v-model="file.author" type="text" name="author" class="form-control" id="author">
                  </div>
                  <div class="input text">
                    <label for="copyright">Copyright</label>
                    <input v-model="file.copyright" type="text" name="copyright" class="form-control" id="copyright">
                  </div>
                </div>
              </div>
            </div>

        </div>
        <p></p>
        <div class="custom-modal-footer">
          <div class="btn-group">
            <button type="button" class="modal-default-button btn btn-success" @click="edit">
              <?= __('Edit') ?>
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
