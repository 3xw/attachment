Vue.component('attachment-files',{
  template: '#attachment-files',
  props: {
    aid:String,
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

    window.aEventHub[this.aid].$on('add-file',this.add);
    window.aEventHub[this.aid].$on('remove-file', this.remove);
    window.aEventHub[this.aid].$on('order-file', this.order);
    window.aEventHub[this.aid].$on('check-ids-file', this.order);

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
      window.aEventHub[this.aid].$emit('add-file-id', file.id);
    },
    remove: function(id){

      var index = this.ids.indexOf(id);
      if(index != -1){
        this.files.splice(index,1);
        this.ids.splice(index,1);
        window.aEventHub[this.aid].$emit('remove-file-id', id);
      }
    },
    move: function (array, old_index, new_index) {
        if (new_index >= array.length) {
            var k = new_index - array.length;
            while ((k--) + 1) {
                array.push(undefined);
            }
        }
        array.splice(new_index, 0, array.splice(old_index, 1)[0]);
    },
    order: function(evt){

      this.move(this.ids,evt.oldIndex,evt.newIndex);
      var file = this.files[evt.oldIndex];
      this.files.splice(evt.oldIndex,1);
      this.files.splice(parseInt(evt.newIndex),0,file);
    }
  }
});
