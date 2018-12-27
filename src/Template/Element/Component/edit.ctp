<!-- upload -->
<script type="text/x-template" id="attachment-edit">
  <div id="attachment-edit" class="modal-mask" v-if="show" transition="modal">
    <div class="modal-wrapper">
      <div class="modal-container container">
        <div class="custom-modal-header">
          <?= __d('Attachment','Edit') ?>
        </div>
        <div class="custom-modal-body">

          <!-- WARNINGS -->
          <div v-for="(error, index) in errors" track-by="$index" class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close"  aria-label="Close" @click="errors = []" ><span aria-hidden="true">&times;</span></button>
            <strong><?= __d('Attachment','Watch out') ?>!</strong> {{error}}
          </div>

          <!-- loading -->
          <div v-if="loading" class="attachment-loading-container">
            <img v-bind:src="settings.url+'attachment/img/loading.gif'" class="img-responsive" />
          </div>

          <!-- TAGS -->
          <attachment-atags :settings="settings"></attachment-atags>

          <!-- INPUTS -->
          <div v-if="show">

            <!-- IF EMBED -->
            <div v-if="file.type == 'embed'">
              <!-- Name HERE -->
              <div class="input text required">
                <label for="name"><?= __d('Attachment','Name') ?></label>
                <input v-model="file.name" type="text" name="name" class="form-control attachment-embed__name" id="name" />
              </div>

              <!-- EMBED CODE HERE -->
              <div class="input text required">
                <label for="embed"><?= __d('Attachment','Embed code') ?></label>
                <textarea v-model="file.embed" name="embed" class="form-control attachment-embed__embed" id="embed" rows="5"></textarea>
              </div>
            </div>

            <!-- IF !EMBED -->
            <div v-if="file.type != 'embed'">
              <div class="input text">
                <label ><?= __d('Attachment','File') ?> </label>
                <p>
                  <!-- file select -->
                  <button v-if="!fileToUpload" @click.prevent="select" role="button" class="btn btn-fill btn-xs btn-info"><?= __d('Attachment','Change file') ?></button>
                  <input v-show="false" type="file" ref="fileInput" @change="validate($event)" :accept="settings.types.join()" />

                  <!-- file info -->
                  <div v-if="fileToUpload" class="alert alert-info" role="alert">
                    <button type="button" class="close"  aria-label="Close" @click.prevent="select" >
                      <span aria-hidden="true">&times; <?= __d('Attachment','Change') ?></span>
                    </button>
                    <strong><?= __d('Attachment','New file to upload') ?>:</strong> {{fileToUpload.name}}
                  </div>
                </p>
              </div>
            </div>

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
                        <input v-model="file.title" type="text" name="title" class="form-control" id="title">
                      </div>
                      <div class="input text">
                        <label for="title"><?= __d('Attachment','Description') ?></label>
                        <textarea v-model="file.description" name="description" class="form-control" id="description" rows="5"></textarea>
                      </div>
                    </div>

                    <!-- other locales -->
                    <div v-for="(language, index) in settings.i18n.languages"  v-if="language != settings.i18n.defaultLocale" role="tabpanel" class="tab-pane active" :id="'a-'+language">
                      <div class="input text">
                        <label for="title"><?= __d('Attachment','Title') ?> {{language}}</label>
                        <input v-model="file['_translations'][language].title" type="text" :name="'_translations['+language+'][title]'" class="form-control" :id="'a-'+language+'-title'">
                      </div>
                      <div class="input text">
                        <label for="title"><?= __d('Attachment','Description') ?> {{language}}</label>
                        <textarea v-model="file['_translations'][language].description" :name="'_translations['+language+'][description]'" class="form-control" :id="'a-'+language+'-description'" rows="5"></textarea>
                      </div>
                    </div>

                  </div>
                </div>

                <!-- no translate -->
                <div v-if="!settings.i18n.enable" class="col-md-6">
                  <div class="input text">
                    <label for="title"><?= __d('Attachment','Title') ?></label>
                    <input v-model="file.title" type="text" name="title" class="form-control" id="title">
                  </div>
                  <div class="input text">
                    <label for="title"><?= __d('Attachment','Description') ?></label>
                    <textarea v-model="file.description" name="description" class="form-control" id="description" rows="5"></textarea>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="input text">
                    <label for="author"><?= __d('Attachment','Author') ?></label>
                    <input v-model="file.author" type="text" name="author" class="form-control" id="author">
                  </div>
                  <div class="input text">
                    <label for="copyright"><?= __d('Attachment','Copyright') ?></label>
                    <input v-model="file.copyright" type="text" name="copyright" class="form-control" id="copyright">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <p></p>
        <div class="custom-modal-footer">
          <div class="btn-group">
            <button type="button" class="modal-default-button btn btn-success" @click="edit">
              <?= __d('Attachment','Edit') ?>
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
