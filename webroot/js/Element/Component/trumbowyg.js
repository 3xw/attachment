Vue.component('attachment-trumbowyg',{
  template: '#attachment-trumbowyg',
  props: {
    settings: Object,
    content: {type: String, default: 'coucou'},
    file: {},
  },
  data:function(){
    return {
      $trumbowyg: null,
      trumbowyg: null,
      //file: null,
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
      this.openOptions();
    },
    'upload-closed': function(){
      this.openOptions();
    },
    'options-success': function(args){
      this.createHtmlElement(args[0]);
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
    openOptions: function(){
      this.file = this.settings.attachments.shift();
      if(this.file){
        this.$broadcast('show-options');
      }
    },
    getImagePath: function(options){
      var path = this.settings.url+'thumbnails/'+this.file.profile+'/';
      path += (options.width)? 'w'+options.width: 'w1200';
      path += (options.crop)? 'c'+options.cropWidth+'-'+options.cropHeight: '';
      path += '/'+this.file.path;
      return path;
    },
    createHtmlElement: function(options){
      var html = '<img';
      var classes = 'img-responsive ';
      classes += (options.classes)? options.classes+' ': '';
      classes += (options.align)? options.align+' ': '';
      html += ' class=\'' + classes + '\'';
      if(options.alt){
        html += ' alt=\'' + options.alt.replace(/['"]+/g, '') + '\'';
      }
      html += ' src=\'' + this.getImagePath(options) + '\'';
      html += ' />';

      var node = $(html)[0];

      if(!this.trumbowyg.range){
        this.trumbowyg.range = this.trumbowyg.doc.getSelection().getRangeAt(0);
        console.log(this.trumbowyg.range);
      }
      this.trumbowyg.range.deleteContents();
      this.trumbowyg.range.insertNode(node);
      return true;
    }
  }
});
