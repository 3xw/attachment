Vue.component('attachment-edit', {
  template: '#attachment-edit',
  data: function(){
    return {
      show:false,
      loading: false,
      atags: [],
      file: {},
      fileToUpload: null,
      errors: [],
      success: [] ,
    };
  },
  props: {
    aid:String,
    settings: Object,
  },
  created: function(){
    var instance = this;
    window.aEventHub[this.aid].$on('edit-file',function(file){
      instance.file = file;
      instance.open();
    });
    window.aEventHub[this.aid].$on('change-tags',this.changeTags);
  },
  methods: {
    close: function(){
      this.show = false;
      this.errors = [];
    },
    open: function(){
      this.atags = this.settings.atags;
      this.show = true;
      setTimeout(this.setupUI, 500);
    },
    changeTags: function(){
      this.atags = $('#atagsinput').val();
    },
    setupUI: function(){
      if(this.settings.i18n.enable){
        $('.attachment-locale-area ul a:last').tab('show');
        $('.attachment-locale-area ul a:first').tab('show');
      }
    },
    validate: function(event)
    {
      this.errors = errors = []
      var file = event.target.files[0]

      // test size
      var size = file.size / 1024 / 1024 ;
      if ( !(size > 0) || !(size <= this.settings.maxsize)) {
        errors.push(file.name +  ' ce fichier est trop lourd. La taille max est de ' + this.settings.maxsize + 'MB.')
      }
      // test
      if(this.settings.types.indexOf(file.type) === false || file.type == "" ){
        errors.push(file.name +  ' ce type de fichier n\' est pas supporté.')
      }
      this.errors = errors;
      if(this.errors.length == 0) this.fileToUpload = file
    },
    select: function()
    {
      this.unselect()
      this.$refs.fileInput.click()
    },
    unselect: function()
    {
      this.fileToUpload = null
      this.errors = []
    },
    edit: function()
    {
      this.close();
      window.aEventHub[this.aid].$emit('edit-progress');

      // start gather data
      this.file.uuid = this.settings.uuid;
      delete(this.file.date);
      delete(this.file.created);
      delete(this.file.modified);

      if($('#atagsinput').length > 0){
        var atags = $('#atagsinput').val();
        this.file.atags = [];
        for( var i in atags )
        {
          this.file.atags[i] = {name: atags[i].trim()}
        }
      }

      // if new file...
      if(this.fileToUpload)
      {
        this.file.path = this.fileToUpload;
        delete(this.file.md5);
        delete(this.file.profile);
        delete(this.file.size);
        delete(this.file.type);
        delete(this.file.subtype);
        delete(this.file.name);
        delete(this.file.width);
        delete(this.file.height);
      }

      var options = {headers:{"Accept":"application/json"}};
      var formData = new FormData();
      for( i in this.file )
      {
          switch(true)
          {
            case this.file[i] == null:
            break;

            case typeof this.file[i] === 'string':
            formData.append(i, this.file[i]);
            break;

            case i == '_translations' && typeof this.file[i] === 'object':
            for(lng in this.file[i]) {
              for (field in this.file[i][lng]) {
                if (field != null) {
                  formData.append(i+'['+lng+']'+'['+field+']', this.file[i][lng][field]);
                  console.log(this.file[i][lng][field]);
                }
              }
            }
            break;

            default:
            formData.append(i, this.file[i]);
            console.log('Unhandled case:', i, this.file[i]);
            break;
          }
      }
      for( var t in this.file.atags )
      {
        formData.append('atags['+t+'][name]', this.file.atags[t].name);
      }
      this.$http.post(this.settings.url+'attachment/attachments/edit/'+this.file.id+'.json', formData,options)
      .then(this.editSuccessCallback, this.errorCallback);
      return this;
    },
    editSuccessCallback: function(response){
      window.aEventHub[this.aid].$emit('edit-success', {response:response, file:this.file});
    },
    errorCallback: function(response){
      window.aEventHub[this.aid].$emit('edit-error', {response:response, file:this.file});
    }
  }
});
