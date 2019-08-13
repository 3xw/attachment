<template lang="html">
  <div class="input form-group">
    <label>{{settings.label}}</label>
    <div class="attachment-input">

      <!-- upload -->
      <attachment-upload :aid="aid" :settings="settings" ></attachment-upload>

      <!-- browse -->
      <attachment-browse :aid="aid" :types="types" :tags="tags" :settings="settings" :from="'input'"></attachment-browse>

      <!-- embed -->
      <attachment-embed :aid="aid" :settings="settings" ></attachment-embed>

      <!-- files -->
      <attachment-files :aid="aid" :settings="settings" ></attachment-files>

      <p>
        <div class="btn-group" data-intro="Ajouter des médias à l'aide de ces boutons" data-position="right">
          <button type="button" class="btn btn-fill btn-xs btn-info" @click="$children[0].open()">
            <i class="fa fa-cloud-upload" aria-hidden="true"></i>
            Upload
          </button>
          <button type="button" class="btn btn-fill btn-xs btn-info" @click="$children[1].open()">
            <i class="fa fa-cloud" aria-hidden="true"></i>
            Browse
          </button>
          <button v-if="dispalyEmbed()" type="button" class="btn btn-fill btn-xs btn-info" @click="$children[2].open()">
            <i class="fa fa-code" aria-hidden="true"></i>
            Add an embed code
          </button>
        </div>
      </p>
    </div>
  </div>
</template>

<script>
export default
{
  name: 'attachment-input',
  props: {
    settings: Object,
    aid:String,
  },
  data: function(){
    return {
      types: null,
      tags: null
    };
  },
  created: function(){
    if(window.aEventHub[this.aid] == undefined){
      window.aEventHub[this.aid] = new Vue();
    }
  },
  methods: {
    dispalyEmbed : function(){
      for( var type in this.settings.types ){
        if(this.settings.types[type].indexOf('embed') != -1)
        {
          return true;
        }
      }
      return false;
    }
  }
}
</script>
