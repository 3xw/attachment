Vue.component('attachment-edit', {
  template: '#attachment-edit',
  data: function(){
    return {
      show:false,
      loading: false,
      atags: [],
      file: {},
      errors: [],
      success: [] ,
    };
  },
  props: {
    settings: Object,
  },
  created: function(){
    window.aEventHub.$on('edit-file', this.editFile);
  },
  methods: {
    editFile: function(file){
      this.file = file;
      this.open();
    },
    close: function(){
      this.show = false;
      this.errors = [];
    },
    open: function(){
      this.atags = this.settings.atags;
      this.show = true;
      setTimeout(this.setupUI, 500);
    },
    setupUI: function(){
      if(this.settings.restrictions.indexOf('tag_restricted') == -1 || this.settings.restrictions.indexOf('tag_or_restricted') != -1){
        $('#atagsinput').tagsinput();
      }
      if(this.settings.i18n.enable){
        $('.attachment-locale-area ul a:last').tab('show');
        $('.attachment-locale-area ul a:first').tab('show');
      }
    },
    edit: function(){
      this.close();
      window.aEventHub.$emit('edit-progress');

      var options = {
        headers:{
          "Accept":"application/json",
          "Content-Type":"application/json"
        }
      };
      this.file.uuid = this.settings.uuid;
      delete(this.file.date);
      delete(this.file.created);
      delete(this.file.modified);
      if(this.settings.restrictions.indexOf('tag_restricted') != -1 || this.settings.restrictions.indexOf('tag_or_restricted') == -1)
      {
        delete(this.file.atags);
      }else
      {
        var atags = $('#atagsinput').val();
        this.file.atags = [];
        for( var i in atags )
        {
          this.file.atags[i] = {name: atags[i].trim()}
        }
      }
      this.$http.post(this.settings.url+'attachment/attachments/edit/'+this.file.id+'.json', this.file,options)
      .then(this.editSuccessCallback, this.errorCallback);
      return this;
    },
    editSuccessCallback: function(response){
      window.aEventHub.$emit('edit-success', {response:response, file:this.file});
    },
    errorCallback: function(response){
      window.aEventHub.$emit('edit-error', {response:response, file:this.file});
    }
  }
});
