Vue.component('attachment-view', {
  template: '#attachment-view',
  data: function(){
    return {
      file: {},
      show: false
    };
  },
  props: {
    aid:String,
    settings: Object,
  },
  created: function(){
    var instance = this;
    window.aEventHub[this.aid].$on('view-file',function(file) {
      console.log(file);
      instance.file = file;
      instance.open();
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
