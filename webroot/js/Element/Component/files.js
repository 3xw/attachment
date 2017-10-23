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
    return {
      files: [],
      ids: []
    };
  },
  created: function(){

    window.aEventHub.$on('add-file',this.add);
    window.aEventHub.$on('remove-file', this.remove);
    window.aEventHub.$on('order-file', this.order);
    window.aEventHub.$on('check-ids-file', this.order);

    for(var i in this.settings.attachments){
      this.files.push(this.settings.attachments[i]);
      this.ids.push(this.settings.attachments[i].id);
    }
  },
  methods: {
    add: function(file){
      if(!this.ids.includes(file.id)){
        if(this.settings.relation == 'belongsTo'){
          this.files = [];
          this.ids = [];
        }else{
          if(this.settings.maxquantity != -1 && (this.ids.length == this.settings.maxquantity)){
            this.files.shift();
            this.ids.shift();
          }
        }
        this.files.push(file);
        this.ids.push(file.id);
      };
      window.aEventHub.$emit('add-file-id', file.id);
    },
    remove: function(id){

      var index = this.ids.indexOf(id);
      if(index != -1){
        this.files.splice(index,1);
        this.ids.splice(index,1);
        window.aEventHub.$emit('remove-file-id', id);
      }
    },
    order: function(evt){
      var index = this.ids.indexOf(evt.item.id);
      if(index != -1){
        var file = this.files[index];
        this.files.splice(evt.oldIndex,1);
        this.files.splice(evt.newIndex,0,file);
        this.ids.splice(evt.oldIndex,1);
        this.ids.splice(evt.newIndex,0,file.id);
      }
    }
  }
});
