Vue.component('attachment-edit', {
  template: '#attachment-edit',
  data: function(){
    return {
      loading: false,
      atags: [],
      files: [],
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
    'yoyo-file': function(file) {
      console.log(file);
      //this.open();
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
      if(this.settings.restrictions.indexOf('tag_restricted') == -1){
        $('#atagsinput').tagsinput();
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
      }
    }
  }
});
