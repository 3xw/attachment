Vue.component('attachment-files-index',{
  template: '#attachment-files-index',
  props: {
    aid:String,
    listStyle: false,
    settings: {
      type: Object,
      required: true
    },
    files: {
      type: Array,
      required: true
    }
  },
  data: function(){
    return {};
  },
  methods: {
    log:function(input){
      console.log(input);
    },
    dispatch:function(evt,aid,data){
      window.aEventHub[this.aid].$emit(evt, data);
    },
  }
});
