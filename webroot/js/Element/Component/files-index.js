Vue.component('attachment-files-index',{
  template: '#attachment-files-index',
  props: {
    listStyle: false,
    settings: {
      type: Object,
      required: true,
      twoWay: true
    },
    files: {
      type: Array,
      required: true,
      twoWay: true
    }
  },
  data: function(){
    return {};
  },
  /*events: {
    'edit-file': function(index) {
      //this.$dispatch('edit-file',index);
      console.log(index);
      return true;
    }
  },*/
  methods: {
  }
});
