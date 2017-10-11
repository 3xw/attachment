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
    getFullLink: function(index){
      var file = this.settings.attachments[index];
      var fullUrl = this.settings.baseUrls[file.profile]+file.path;
      this.showLink(fullUrl);
    },
    onEnd: function(evt){
      var file = this.settings.attachments[evt.item.id];
      this.settings.attachments.splice(evt.oldIndex,1);
      this.settings.attachments.splice(evt.newIndex,0,file);
    },
    showLink(val){
      $('#attachment-full-url').remove();
      $('#attachment-files').after('<div id="attachment-full-url"><label>Attachment Full URL</label><input type="text" class="form-control" value="'+val+'"/></a></div>');
    }
  }
});
