(function(scope, $){

  Vue.config.devtools = true;

  Vue.filter('bytesToMegaBytes', function (input) {
    return input / 1024 / 1024;
  });

  Vue.filter('decimal', function (input, places) {
    if (isNaN(input)) return input;
    var factor = "1" + Array(+(places > 0 && places + 1)).join("0");
    return Math.round(input * factor) / factor;
  });

  Vue.filter('icon', function (input) {
    switch(input){
      case 'image/jpeg':
      case 'image/png':
      case 'image/gif':
      return '<i class="fa fa-file-image-o" aria-hidden="true"></i>';
      break;
      case 'application/pdf':
      return '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>';
      break;
      case 'video/mp4':
      case 'video/ogg':
      return '<i class="fa fa-file-video-o" aria-hidden="true"></i>';
      break;
      case 'audio/mp4':
      case 'audio/ogg':
      return '<i class="fa fa-file-audio-o" aria-hidden="true"></i>';
      break;
      case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
      return '<i class="fa fa-file-excel-o" aria-hidden="true"></i>';
      break;
      case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
      return '<i class="fa fa-file-text-o" aria-hidden="true"></i>';
      break;
      case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
      return '<i class="fa fa-file-powerpoint-o" aria-hidden="true"></i>';
      break;
      default:
      return '<i class="fa fa-file-file-o" aria-hidden="true"></i>';
    }
  });

  Vue.directive('sortable', function (options) {
    options = options || {}
    scope.Sortable.create(this.el, options);
  });

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
        required: true,
        twoWay: true
      }
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
        if ('speechSynthesis' in window)
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
          url: this.settings.addURL,
          data: formData,
          cache: false,
          contentType: false,
          processData: false,
          xhr: function() {
            var xhr = new scope.XMLHttpRequest();

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
            self.$parent.selectedfiles.push(response.data);
            self.handleUploads();
          },
          error: function(response){
            self.progress = false;
            self.$els.progressbar.style.width = 0 + "%";
            //self.success = '';
            var errors = response.responseJSON.data.errors;
            var message = response.responseJSON.data.message;
            if(errors['md5']){
              if(errors['md5']['unique']){
                message = errors['md5'].unique;
              }
            }
            self.errors.push(fileName+': '+message);
            self.handleUploads();
          }
        });
      }
    }
  });

  Vue.component('attachment-pagination',{
    template: '#attachment-pagination',
    props: {
      pagination: {
        type: Object,
        required: true
      },
      callback: {
        type: Function,
        required: true
      },
      offset: {
        type: Number,
        default: 4
      }
    },
    computed: {
      array: function () {
        if(this.pagination.page_count == 1) {
          return [];
        }

        var from = this.pagination.current_page - this.offset;
        if(from < 1) {
          from = 1;
        }

        var to = from + (this.offset );
        if(to >= this.pagination.page_count) {
          to = this.pagination.page_count;
        }

        var arr = [];
        while (from <=to) {
          arr.push(from);
          from++;
        }

        return arr;
      }
    },
    methods: {
      changePage: function (page) {
        this.pagination.current_page = page;
        this.callback();
      }
    }
  });

  Vue.component('attachment-filters',{
    template: '#attachment-filters',
    props: {
      tags: Array,
      types: Array,
      callback: {
        type: Function,
        required: true
      }
    },
    data: function(){
      return {
        sort: { query: '', term: ''}
      }
    },
    methods: {
      find: function(){
        this.$parent.pagination.current_page = 1;
        this.$parent.search = $('#searchInputSearch').val() || null;
        this.$parent.tag = $('#tagsInputSearch').val() || null;
        this.callback();
      },
      setupUI: function(){

        var tagsList = new Bloodhound({
          datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
          queryTokenizer: Bloodhound.tokenizers.whitespace,
          local: this.tags
        });
        tagsList.initialize();

        $('#tagsInputSearch').tagsinput({
          freeInput: true,
          itemValue: 'name',
          itemText: 'name',
          typeaheadjs: {
              name: 'tagsList',
              displayKey: 'name',
              source: tagsList.ttAdapter()
          }
        });

        $('#tagsInputSearch').change(this.clearSearch);
        $('#searchInputSearch').keyup(this.clearTags);

      },
      clearTags: function(){
        console.log('clearTags');
        if($('#tagsInputSearch').val()){
          $('#tagsInputSearch').tagsinput('removeAll');
        }
      },
      clearSearch: function(e, force){
        console.log('clearSearch');
        console.log($('#tagsInputSearch').val());
        console.log(force);
        if( $('#tagsInputSearch').val() != null || force){
          $('#searchInputSearch').val('');
        }
      }
    }
  });

  Vue.component('attachment-browse', {
    template: '#attachment-browse',
    data: function(){
      return {
        search: '',
        tag: '',
        files: [],
        ids: [],
        pagination: {
          "page_count": 1,
          "current_page": 1,
          "has_next_page": false,
          "has_prev_page": false,
          "count": 0,
          "limit": 100
        }
      };
    },
    props: {
      selectedfiles: Array,
      tags: Array,
      types: Array,
      settings: Object,
      show: {
        type: Boolean,
        required: true,
        twoWay: true
      }
    },
    watch: {
      'selectedfiles' : function(val, oldVal){
        var newIds = [];
        for(var i in val ){
          newIds.push(val[i].id);
        }
        this.ids = newIds;
      }
    },
    ready: function(){
      for(var i in this.settings.attachments ){
        this.ids.push(this.settings.attachments[i].id);
      }
    },
    methods: {
      isSelected: function(id){
        return this.ids.indexOf(id) != -1;
      },
      addEventListeners : function(){
        $('form').bind('submit', this.preventSubmit);
      },
      removeEventListeners : function(){
        $('form').unbind('submit', this.preventSubmit);
      },
      preventSubmit: function(e){
        e.preventDefault();
      },
      close: function(){
        this.removeEventListeners();
        this.files = [];
        this.show = false;
      },
      open: function(){
        this.addEventListeners();
        this.show = true;
        this.getFiles();
        setTimeout(this.setupUI, 500);
      },
      setupUI: function(){
        this.$children[0].setupUI();
      },
      getFiles: function(){
        var params = {page: this.pagination.current_page};
        if(this.search){
          params.search = this.search;
        }
        if(this.tag){
          params.tag = this.tag;
        }
        var options = {
          params: params,
          headers:{
            "Accept":"application/json",
            "Content-Type":"application/json"
          }
        };
        this.$http.get(this.settings.browseURL, options)
        .then(this.successCallback, this.errorCallback);
      },
      successCallback: function(response){
        this.files = response.data.data;
        this.pagination = response.data.pagination;
        //console.log(this.pagination);
      },
      errorCallback: function(response){
        console.log(response);
      },
      add: function(index){
        this.selectedfiles.push(this.files[index]);
      },
      remove: function(id){
        var index = this.ids.indexOf(id);
        this.selectedfiles.splice(index,1);
      }
    }
  });

  Vue.component('attachment-files',{
    template: '#attachment-files',
    props: {
      files: {
        type: Array,
        required: true,
        twoWay: true
      }
    },
    data: function(){
      return {};
    },
    methods: {
      remove: function(index){
        this.files.splice(index,1);
      },
      onEnd: function(evt){
        var file = this.files[evt.item.id];
        this.files.splice(evt.oldIndex,1);
        this.files.splice(evt.newIndex,0,file);
      }
    }
  });

  var app = new Vue({
    el: "#attachment-app",
    data: {
      showUpload: false,
      showBrowse: false,
      settings: $('#attachment-settings').data('settings'),
      selectedfiles1: [],
      tags: [],
      types: []
    },
    ready:function(){
      this.selectedfiles1 = this.settings.attachments;
      this.getTags().getTypes();
    },
    methods: {
      getTags: function(){
        var options = {
          headers:{
            "Accept":"application/json",
            "Content-Type":"application/json"
          }
        };
        this.$http.get(this.settings.tagsURL, options)
        .then(this.tagsSuccessCallback, this.errorCallback);
        return this;
      },
      getTypes: function(){
        var options = {
          params: {
            types: '*',
          },
          headers:{
            "Accept":"application/json",
            "Content-Type":"application/json"
          }
        };
        this.$http.get(this.settings.browseURL, options)
        .then(this.typesSuccessCallback, this.errorCallback);
        return this;
      },
      tagsSuccessCallback: function(response){
        this.tags = response.data.data;
      },
      typesSuccessCallback: function(response){
        this.types = response.data.data;
      },
      errorCallback: function(response){
        console.log(response);
      },
    }
  });

})(window, jQuery);
