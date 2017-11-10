Vue.component('attachment-trumbowyg-options',{
  template: '#attachment-trumbowyg-options',
  props: {
    settings: Object,
    file: {},
    show: {
      type: Boolean,
      default: false
    },
  },
  data: function(){
    return {
      options: {
        crop: false,
        align: ''
      }
    }
  },
  created: function(){
    window.aEventHub[this.aid].$on('show-options', function(){
      this.addEventListeners();
      this.show = true;
    });
  },
  methods: {
    addEventListeners : function(){
      $(document).bind('keypress', this.preventEnter);
      $('form').bind('submit', this.preventSubmit);
    },
    removeEventListeners : function(){
      $(document).unbind('keypress', this.preventEnter);
      $('form').unbind('submit', this.preventSubmit);
    },
    preventEnter: function(e){
      if(e.which == 13) {
        this.preventSubmit(e);
      }
    },
    preventSubmit: function(e){
      e.preventDefault();
      e.stopPropagation();
    },
    close: function(){
      this.removeEventListeners();
      this.show = false;
    },
    success: function(){
      this.close();
      window.aEventHub[this.aid].$emit('options-success', [this.options]);
    }
  }
});
