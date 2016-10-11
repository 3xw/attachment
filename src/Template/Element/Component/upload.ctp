<!-- upload -->
<script type="text/x-template" id="attachment-upload">
  <div id="attachment-upload" class="modal-mask" v-if="show" transition="modal">
    <div class="modal-wrapper">
      <div class="modal-container container">
        <div class="custom-modal-header">
          <?= __('Téléverser des fichiers') ?>
        </div>
        <div class="custom-modal-body">

          <!-- WARNINGS -->
          <div v-for="(index, error) in errors" track-by="$index" class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close"  aria-label="Close" @click="errors = []" ><span aria-hidden="true">&times;</span></button>
            <strong>Attention!</strong> {{error}}
          </div>

          <!-- PROGRESS -->
          <div class="progress" v-show="progress">
    			   <div class="progress-bar" style="width: 0%;" v-el:progressbar ></div>
    		  </div>

          <!-- TAGS -->
          <div  id="attachment-atags">
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
                    <label for="title">Description</label>
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

            <!-- DROPZONE -->
            <div v-if="files.length == 0"  id="attachment-dropzone">
              <label >Fichiers</label>
              <div class="attachment-dropzone__area">
                <div class="attachment-dropzone__instructions">
                <strong>Glissez déposez des médias ici, ou utilisez le bouton ci-dessous!</strong><br/>
                ( taille max: {{settings.maxsize}} MB )
                <input id="attachment-files-input" type="file"  name="files[]" multiple="multiple" data-max-upload-size="100">
                </div>
              </div>
            </div>
          </div>

          <!-- FILE LIST -->
          <div v-for="(index, file) in files" class="attachment-upload__item">
            <div class="attachment-upload__item__icon">
              {{{ file.type | icon }}}
            </div>
            <div  class="attachment-upload__item__description">
              <strong>{{ file.name }}</strong><br/>
              type: {{ file.type }}<br/>
              size: {{ file.size | bytesToMegaBytes | decimal 3 }} MB<br/>
            </div>
          </div>

        </div>
        <p></p>
        <div class="custom-modal-footer">
          <div class="btn-group">
            <button v-if="files.length" type="button" class="modal-default-button btn btn-success" @click="startUpload">
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
