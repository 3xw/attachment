<template lang="html">
  <div id="attachment-files" data-intro="Liste des médias séléctionnés, vous pouvez les ordonner en les déplaçant à la souris." data-position="left">

    <!-- add Array input -->
    <input v-if="settings.relation == 'belongsToMany'" type="hidden" name="attachments[]" value="" id="AttachmentAttachment_">

    <!-- BelongsTo default value -->
    <input v-if="settings.relation != 'belongsToMany'" type="hidden" :name="settings.field" value="0">

    <div class="row" v-sortable="{draggable:'.attachment-files__item', onEnd:order}" >
      <div v-for="(file, index) in files" :id="file.id"  class="attachment-files__item" v-bind:class="settings.cols" :key="file.id">
        <div class="card mb-4" >

          <!-- thumb -->
          <attachment-thumb :url="settings.url" :file="file"></attachment-thumb>

          <div class="card-body">
              <p class="card-text small">
                <span v-if="file.title">{{file.title | truncate(15) }}<br/></span>
                {{file.name | truncate(15) }}<br/>
                {{file.size | bytesToMegaBytes | decimal(2) }} MB<br/>
              </p>
            <!-- data -->
            <input v-if="settings.relation == 'belongsToMany'" type="hidden" :name="'attachments['+index+'][id]'" :value="file.id">
            <input v-if="settings.relation == 'belongsToMany'" type="hidden" :name="'attachments['+index+'][_joinData][order]'" :value="index">

            <input v-if="settings.relation != 'belongsToMany'" type="hidden" :name="settings.field" :value="file.id">


            <button class="btn btn-fill btn-xs btn-warning" v-on:click.prevent="remove(file.id)" >Remove</button>
          </div>
        </div><!-- end card -->
      </div>
    </div>
  </div>
</template>

<script>
export default
{
  name: 'attachment-files',
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
}
</script>
