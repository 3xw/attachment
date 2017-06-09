Vue.component('attachment-trumbowyg',{
  template: '#attachment-trumbowyg',
  props: {
    settings: Object,
    content: {type: String, default: 'coucou'},
  },
  data:function(){
    return {
      $trumbowyg: null,
      trumbowyg: null,
      file: null,
      options: {
        classes: {
          label: 'Styles',
          required: true
        },
      }
    };
  },
  events: {
    'browse-closed': function(){
      this.openModal();
    },
    'upload-closed': function(){
      this.openModal();
    }
  },
  ready: function(){
    this.$trumbowyg = $('.'+this.settings.uuid);
    this.$trumbowyg
    .trumbowyg(this.settings.trumbowyg)
    .on('attachment-upload',this.upload)
    .on('attachment-browse',this.browse)
  },
  methods: {
    setup: function(trumbowyg){
      this.trumbowyg = trumbowyg;
      this.settings.attachments = [];
      this.file = null;
    },
    upload: function(evt, trumbowyg){
      this.setup(trumbowyg);
      this.$broadcast('show-upload');
    },
    browse: function(evt, trumbowyg){
      this.setup(trumbowyg);
      this.$broadcast('show-browse');
    },
    openModal: function(){
      this.file = this.settings.attachments.shift();
      if(this.file){
        this.trumbowyg.openModalInsert(this.trumbowyg.lang['attachment-settings'], this.options, this.createHtmlElement);
      }
    },
    getImagePath: function(){
      var path = this.settings.url+'thumbnails/'+this.file.profile+'/';
      path += 'w1200/'+this.file.path;
      return path;
    },
    createHtmlElement: function(options){
      var html = '<img';
      if (options.classes) {
        html += ' class=\'' + options.classes + '\'';
      }

      html += ' src=\'' + this.getImagePath() + '\'';

      html += ' />';
      var node = $(html)[0];
      this.trumbowyg.range.deleteContents();
      this.trumbowyg.range.insertNode(node);
      return true;
    }
  }
});
