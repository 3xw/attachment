Vue.component('attachment-upload', {
  template: '#attachment-upload',
  data: function(){
    return {
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
    settings: Object,
    show: {
      type: Boolean,
      default: false
    },
  },
  methods: {
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
    },
    open: function(){
      this.atags = this.settings.atags;
      this.show = true;
      setTimeout(this.setupUI, 500);
    },
    setupUI: function(){
      this.addEventListeners();
      $('#atagsinput').tagsinput();
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
      var tags = $('#atagsinput').val();
      for( var t in tags )
      {
        formData.append('atags['+t+'][name]', tags[t].trim());
      }
      $('.optional-fields input, .optional-fields textarea, .optional-fields select').each(function(){
        var $input = $(this);
        var value = $input.val();
        if(value)
        {
            formData.append($input.attr('name'), value.trim());
        }
      });

      $.ajax({
        type: 'POST',
        dataType: 'json',
        url: this.settings.url+'attachment/attachments/add.json',
        data: formData,
        //context: document.body,
        cache: false,
        contentType: false,
        processData: false,
        xhr: function() {
          var xhr = new window.XMLHttpRequest();

          xhr.upload.addEventListener("progress", function(evt){
            if (evt.lengthComputable) {
              var percentComplete = ( (evt.loaded / evt.total)  * 100 ) + "%";
              self.progress = true;
              self.$els.progressbar.style.width = percentComplete;
            }
          }, false);

          return xhr;
        },

        success: function(response){
          self.progress = false;
          self.$els.progressbar.style.width = 0+ "%";
          self.settings.attachments.push(response.data);
          self.handleUploads();
        },
        error: function(response){
          self.progress = false;
          self.$els.progressbar.style.width = 0 + "%";

          var message = response.statusText;
          //self.success = '';
          if(response.responseJSON){
            var errors = response.responseJSON.data.errors;
            var message = response.responseJSON.data.message;
            if(errors['md5']){
              if(errors['md5']['unique']){
                message = errors['md5'].unique;
              }
            }
          }
          self.errors.push(fileName+': '+message);
          self.handleUploads();
        }
      });
    }
  }
});
