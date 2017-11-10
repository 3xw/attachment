Vue.component('attachment-view', {
  template: '#attachment-view',
  data: function(){
    return {
      file: {},
    };
  },
  props: {
    aid:String,
    settings: Object,
    show: {
      type: Boolean,
      default: false
    },
  },
  created: function(){
    window.aEventHub[this.aid].$on('view-file',function(file) {
      this.file = file;
      this.open();
    });
  },
  methods: {
    close: function(){
      this.show = false;
    },
    open: function(){
      this.show = true;
      setTimeout(this.setupUI, 500);
    },
    setupUI: function(){
      console.log('setup UI');
    },
  }
});
