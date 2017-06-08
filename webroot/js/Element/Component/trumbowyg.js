Vue.component('attachment-trumbowyg',{
  template: '#attachment-trumbowyg',
  props: {
    settings: Object,
    content: {type: String, default: 'coucou'},
  },
  data:function(){
    return {
      $trumbowyg: null
    };
  },
  watch: {
    'settings.attachments' : function(val, oldVal){
      // ici updater le text et remplacer par des images
    }
  },
  ready: function(){
    /*
    $.trumbowyg.svgPath = this.svgPath;
    $('#trumbowyg-editor')
    .trumbowyg({ lang: this.language, })
    .on('tbwchange', this.onChange);
    $('#trumbowyg-editor').trumbowyg('html', this.content);
    */
    console.log('ready');
    this.$trumbowyg = $('.'+this.settings.uuid);
    this.$trumbowyg.trumbowyg();
  },
  methods: {

  }
});
