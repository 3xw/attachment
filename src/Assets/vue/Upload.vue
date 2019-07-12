<template lang="html">
  <div id="attachment-upload" class="modal-mask" v-if="show" transition="modal">
    <div class="modal-wrapper">
      <div class="modal-container container">
        <div class="custom-modal-header">
          Upload files
        </div>
        <div class="custom-modal-body">

          <!-- WARNINGS -->
          <div v-for="(error, index) in errors" track-by="$index" class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close"  aria-label="Close" @click="errors = []" ><span aria-hidden="true">&times;</span></button>
            <strong>Watch out!</strong> {{error}}
          </div>

          <!-- PROGRESS -->
          <div class="progress" v-show="progress">
             <div class="progress-bar" style="width: 0%;" ref="progressbar" ></div>
          </div>

          <!-- TAGS -->
          <attachment-atags :aid="aid" :settings="settings"></attachment-atags>

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
                        <label for="title">Title</label>
                        <input type="text" name="title" class="form-control" id="title">
                      </div>
                      <div class="input text">
                        <label for="title">Description</label>
                        <textarea name="description" class="form-control" id="description" rows="5"></textarea>
                      </div>
                    </div>

                    <!-- other locales -->
                    <div v-for="(language, index) in settings.i18n.languages"  v-if="language != settings.i18n.defaultLocale" role="tabpanel" class="tab-pane active" :id="'a-'+language">
                      <div class="input text">
                        <label for="title">Title {{language}}</label>
                        <input type="text" :name="'_translations['+language+'][title]'" class="form-control" :id="'a-'+language+'-title'">
                      </div>
                      <div class="input text">
                        <label for="title">Description {{language}}</label>
                        <textarea :name="'_translations['+language+'][description]'" class="form-control" :id="'a-'+language+'-description'" rows="5"></textarea>
                      </div>
                    </div>

                  </div>
                </div>

                <!-- no translate -->
                <div v-if="!settings.i18n.enable" class="col-md-6">
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
              <label >Files</label>
              <div class="attachment-dropzone__area">
                <div class="attachment-dropzone__instructions">
                <strong>Drag and drop files here, or click on the button</strong><br/>
                ( Max size: {{settings.maxsize}} MB )
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
              type: {{ file.type }}<br/>
              size: {{ file.size | bytesToMegaBytes | decimal(3) }} MB<br/>
            </div>
          </div>

        </div>
        <p></p>
        <div class="custom-modal-footer">
          <div class="btn-group">
            <button v-if="files.length" type="button" class="modal-default-button btn btn-success" @click="startUpload">
              Upload
            </button>
            <button type="button" class="modal-default-button btn btn-warning" @click="close()">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default
{
  name: 'attachment-upload',
  data: function(){
    return {
      show: false,
      progress: false,
      atags: [],
      files: [],
      errors: [],
      success: [] ,
      greenBack: '#bbf7bd',
      greenBorder: '3px dotted #219249',
      grayBack: '#eee',
      grayBorder: '3px dotted #808080',
    };
  },
  props: {
    aid:String,
    settings: Object,
  },
  created: function(){
    window.aEventHub[this.aid].$on('show-upload',this.showUpload);
    window.aEventHub[this.aid].$on('change-tags',this.changeTags);
  },
  methods: {
    showUpload: function(){
      this.open();

    },
    changeTags: function(){
      this.atags = $('#atagsinput').val();
    },
    dragOver: function(e){
      e.preventDefault();
      $('#attachment-dropzone').css({
        border: this.greenBorder,
        background: this.greenBack,
      });
    },
    dragOut: function(){
      $('#attachment-dropzone').css({
        border: this.grayBorder,
        background: this.grayBack,
      });
    },
    getDroppedFiles: function(e){
      e.preventDefault();
      e.stopPropagation();
      this.dragOut();
      this.validate(e.originalEvent.dataTransfer.files);
    },
    getselectedfiles: function(){
      this.validate($('#attachment-files-input').prop("files"));
    },
    validate: function(fileList){
      var errors = [];
      for(var i = 0; i < fileList.length; i++){
        // test size
        var size = fileList[i].size / 1024 / 1024 ;
        if ( !(size > 0) || !(size <= this.settings.maxsize)) {
          errors.push(fileList[i].name +  ' ce fichier est trop lourd. La taille max est de ' + this.settings.maxsize + 'MB.');
          continue;
        }
        // test
        if(this.settings.types.indexOf(fileList[i].type) === false || fileList[i].type == "" ){
          errors.push(fileList[i].name +  ' ce type de fichier n\' est pas supporté.');
          continue;
        }
        // add file to list :)
        this.files.push(fileList[i]);
      }
      this.errors = errors;
    },
    addEventListeners : function(){
      $('#attachment-dropzone').bind('dragenter', this.dragOver);
      $('#attachment-dropzone').bind('dragleave', this.dragOut);
      $('#attachment-dropzone').bind('dragover', this.dragOver);
      $('#attachment-dropzone').bind('drop', this.getDroppedFiles);
      $('#attachment-files-input').bind('change', this.getselectedfiles);
    },
    removeEventListeners : function(){
      $('#attachment-dropzone').unbind('dragenter', this.dragOver);
      $('#attachment-dropzone').unbind('dragleave', this.dragOut);
      $('#attachment-dropzone').unbind('dragover', this.dragOver);
      $('#attachment-dropzone').unbind('drop', this.getDroppedFiles);
      $('#attachment-files-input').unbind('change', this.getselectedfiles);
    },
    close: function(){
      this.removeEventListeners();
      this.files = [];
      this.show = false;
      this.errors = [];
      window.aEventHub[this.aid].$emit('upload-closed')
    },
    open: function(){
      this.atags = this.settings.atags;
      if(this.settings.atagsDisplay == 'select'){
        this.atags = [];
      }
      this.show = true;
      setTimeout(this.setupUI, 500);
    },
    setupUI: function(){
      this.addEventListeners();
      if(this.settings.i18n.enable){
        $('.attachment-locale-area ul a:last').tab('show');
        $('.attachment-locale-area ul a:first').tab('show');
      }
    },
    startUpload: function(){
      this.errors = [];
      this.handleUploads();
    },
    handleUploads: function(){
      if(this.files.length != 0){
        this.upload(this.files.shift());
      }else{
        if(this.errors.length == 0 ){
          this.close();
          this.tellUser('Tous les fichiers ont bien été téléversé!');
        }else{
          this.removeEventListeners();
          this.addEventListeners();
          this.tellUser(this.errors.length+' fichiers n\'ont pu être téléverssé!');
        }
        window.aEventHub[this.aid].$emit('upload-finished');
      }
    },
    tellUser: function(message){
      if ('speechSynthesis' in window && this.settings.speech == true )
      {
        var msg = new SpeechSynthesisUtterance(message);
        msg.lang = 'fr-FR';
        window.speechSynthesis.speak(msg);
      }
    },
    upload: function(file){
      var self = this;

      var formData = new FormData();
      formData.append('path', file);
      formData.append('uuid', this.settings.uuid);
      var fileName = file.name;

      // retrieve tags
      var tags = (this.settings.atagsDisplay == 'input')? $('#atagsinput').val() : this.atags;
      for( var t in tags )
      {
        formData.append('atags['+t+'][name]', tags[t].trim());
      }
      $('.optional-fields input, .optional-fields textarea, .optional-fields select').each(function(){
        var $input = $(this);
        var value = $input.val();
        formData.append($input.attr('name'), value.trim());
      });

      $.ajax({
        type: 'POST',
        dataType: 'json',
        url: this.settings.url+'attachment/attachments/add.json',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        xhr: function() {
          var xhr = new window.XMLHttpRequest();

          xhr.upload.addEventListener("progress", function(evt){
            if (evt.lengthComputable) {
              var percentComplete = ( (evt.loaded / evt.total)  * 100 ) + "%";
              self.progress = true;
              self.$refs.progressbar.style.width = percentComplete;
            }
          }, false);

          return xhr;
        },

        success: function(response){
          self.progress = false;
          self.$refs.progressbar.style.width = 0+ "%";
          if(self.settings.relation != 'belongsToMany'){
            if(self.settings.attachments.length > 0){
              self.settings.attachments.pop();
            }
          }else{
            if(self.settings.maxquantity != -1 && (self.settings.attachments.length == self.settings.maxquantity)){
              self.settings.attachments.shift();
            }
          }
          if(!self.settings.attachments){ self.settings.attachments = []; }
          self.settings.attachments.push(response.data);
          window.aEventHub[self.aid].$emit('add-file', response.data);
          self.handleUploads();
        },
        error: function(response){
          self.progress = false;
          self.$refs.progressbar.style.width = 0 + "%";

          var message = response.statusText;

          //self.success = '';
          if(response.responseJSON){
            var errors = ( response.responseJSON['data'] )? response.responseJSON.data.errors: response.responseJSON.errors;
            var message = ( response.responseJSON['data'] )? response.responseJSON.data.message: response.responseJSON.message;

            if(errors){
              if(errors['md5']){
                if(errors['md5']['unique']){
                  message = errors['md5'].unique;
                }
              }
            }
          }
          self.errors.push(fileName+': '+message);
          self.handleUploads();
        }
      });
    }
  }
}
</script>
