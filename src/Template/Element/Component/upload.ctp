<!-- upload -->
<script type="text/x-template" id="attachment-upload">
  <div id="attachment-upload" class="modal-mask" v-if="show" transition="modal">
    <div class="modal-wrapper">
      <div class="modal-container container">
        <div class="custom-modal-header">
          <?= __d('Attachment','Upload files') ?>
        </div>
        <div class="custom-modal-body">

          <!-- WARNINGS -->
          <div v-for="(error, index) in errors" track-by="$index" class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close"  aria-label="Close" @click="errors = []" ><span aria-hidden="true">&times;</span></button>
            <strong><?= __d('Attachment','Watch out') ?>!</strong> {{error}}
          </div>

          <!-- PROGRESS -->
          <div class="progress" v-show="progress">
    			   <div class="progress-bar" style="width: 0%;" ref="progressbar" ></div>
    		  </div>

          <!-- TAGS -->
          <div v-if="this.settings.restrictions.indexOf('tag_restricted') == -1 && this.settings.restrictions.indexOf('tag_or_restricted') == -1" id="attachment-atags">
            <label ><?= __d('Attachment','Tags') ?></label>
            <select id="atagsinput" multiple class="form-control">
              <option v-for="(atag, index) in atags" :value="atag">{{atag}}</option>
            </select>
          </div>

          <!-- INPUTS -->
          <div v-if="show">

            <!-- OPTIONS -->
            <div  id="attachment-options">
              <div class="row optional-fields">

                <!-- _translations[en_GB][name] -->
                <div v-if="settings.i18n.enable" class="col-md-6 attachment-locale-area">
                  <ul class="nav nav-tabs" role="tablist">
                    <li v-for="(language, index) in settings.i18n.languages" v-bind:class="{ 'active': language ==  settings.i18n.defaultLocale}" role="presentation">
                      <a :href="'#a-'+language" :aria-controls="'a-'+language" role="tab" data-toggle="tab" >
                        {{language}}
                      </a>
                    </li>
                  </ul>
                  <div class="tab-content">

                    <!-- defaultLocale -->
                    <div role="tabpanel" class="tab-pane active" :id="'a-'+settings.i18n.defaultLocale">
                      <div class="input text">
                        <label for="title"><?= __d('Attachment','Title') ?></label>
                        <input type="text" name="title" class="form-control" id="title">
                      </div>
                      <div class="input text">
                        <label for="title"><?= __d('Attachment','Description') ?></label>
                        <textarea name="description" class="form-control" id="description" rows="5"></textarea>
                      </div>
                    </div>

                    <!-- other locales -->
                    <div v-for="(language, index) in settings.i18n.languages"  v-if="language != settings.i18n.defaultLocale" role="tabpanel" class="tab-pane active" :id="'a-'+language">
                      <div class="input text">
                        <label for="title"><?= __d('Attachment','Title') ?> {{language}}</label>
                        <input type="text" :name="'_translations['+language+'][title]'" class="form-control" :id="'a-'+language+'-title'">
                      </div>
                      <div class="input text">
                        <label for="title"><?= __d('Attachment','Description') ?> {{language}}</label>
                        <textarea :name="'_translations['+language+'][description]'" class="form-control" :id="'a-'+language+'-description'" rows="5"></textarea>
                      </div>
                    </div>

                  </div>
                </div>

                <!-- no translate -->
                <div v-if="!settings.i18n.enable" class="col-md-6">
                  <div class="input text">
                    <label for="title"><?= __d('Attachment','Title') ?></label>
                    <input type="text" name="title" class="form-control" id="title">
                  </div>
                  <div class="input text">
                    <label for="title"><?= __d('Attachment','Description') ?></label>
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

            <!-- DROPZONE -->
            <div v-if="files.length == 0"  id="attachment-dropzone">
              <label ><?= __d('Attachment','Files') ?></label>
              <div class="attachment-dropzone__area">
                <div class="attachment-dropzone__instructions">
                <strong><?= __d('Attachment','Drag and drop files here, or click on the button') ?></strong><br/>
                ( <?= __d('Attachment','Max size') ?>: {{settings.maxsize}} MB )
                <input id="attachment-files-input" type="file"  name="files[]" multiple="multiple" data-max-upload-size="100">
                </div>
              </div>
            </div>
          </div>

          <!-- FILE LIST -->
          <div v-for="(file, index) in files" class="attachment-upload__item">
            <div class="attachment-upload__item__icon" v-html="$options.filters.icon(file.type)">

            </div>
            <div  class="attachment-upload__item__description">
              <strong>{{ file.name }}</strong><br/>
              <?= __d('Attachment','type') ?>: {{ file.type }}<br/>
              <?= __d('Attachment','size') ?>: {{ file.size | bytesToMegaBytes | decimal(3) }} MB<br/>
            </div>
          </div>

        </div>
        <p></p>
        <div class="custom-modal-footer">
          <div class="btn-group">
            <button v-if="files.length" type="button" class="modal-default-button btn btn-success" @click="startUpload">
              <?= __d('Attachment','Upload') ?>
            </button>
            <button type="button" class="modal-default-button btn btn-warning" @click="close()">
              <?= __d('Attachment','Close') ?>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</script>
