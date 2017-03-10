Vue.component('attachment-edit', {
  template: '#attachment-edit',
  data: function(){
    return {
      loading: false,
      atags: [],
      file: {},
      errors: [],
      success: [] ,
    };
  },
  props: {
    settings: Object,
    show: {
      type: Boolean,
      default: false
    },
  },
  events: {
    'edit-file': function(file) {
      this.file = file;
      this.open();
    }
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
      this.$dispatch('edit-progress');

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
      if(this.settings.restrictions.indexOf('tag_restricted') != -1 || this.settings.restrictions.indexOf('tag_or_restricted') != -1)
      {
        delete(this.file.atags);
      }
      this.$http.post(this.settings.url+'attachment/attachments/edit/'+this.file.id+'.json', this.file,options)
      .then(this.editSuccessCallback, this.errorCallback);
      return this;
    },
    editSuccessCallback: function(response){
      this.$dispatch('edit-success', response, this.file);
    },
    errorCallback: function(response){
      this.$dispatch('edit-error', response, this.file);
    }
  }
});
