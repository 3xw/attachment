Vue.component('attachment-files-index',{
  template: '#attachment-files-index',
  props: {
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
    dispatch(evt, data){
      window.aEventHub.$emit(evt, data);
    },
  }
});
