Vue.component('attachment-files',{
  template: '#attachment-files',
  props: {
    settings: {
      type: Object,
      required: true,
      twoWay: true
    }
  },
  data: function(){
    return {};
  },
  methods: {
    remove: function(index){
      this.settings.attachments.splice(index,1);
    },
    onEnd: function(evt){
      var file = this.settings.attachments[evt.item.id];
      this.settings.attachments.splice(evt.oldIndex,1);
      this.settings.attachments.splice(evt.newIndex,0,file);
    }
  }
});
