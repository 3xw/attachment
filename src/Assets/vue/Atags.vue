<template lang="html">
  <div id="attachment-atags" v-if="settings.atagsDisplay != false">
    <label>Tags</label>
    <select name="atags" id="atagsinput" multiple class="form-control">
      <option :selected="(activeTags.indexOf(atag) !== -1)? 'selected' : false" v-for="atag in atags" :value="atag">
        {{atag}}
      </option>
    </select>
  </div>
</template>

<script>
export default
{
  name:'attachment-atags',
  props: {
    aid:String,
    settings: {
      type: Object,
      required: true,
      twoWay: true
    },
    file: Object
  },
  data: function(){
    return {
      atags: [],
      activeTags: []
    }
  },
  created: function()
  {
    this.atags = this.settings.atags;
    if(this.file !== undefined){
      for(i = 0;i < this.file.atags.length;i++){
        this.activeTags.push(this.file.atags[i].name)
      }
    }
    $('body').append('<style>.select2-dropdown{z-index: 70000 !important;}</style>')
  },
  mounted: function(){
    var self = this;
    if(this.settings.atagsDisplay == 'select'){
      $('#atagsinput').select2()
      $('#atagsinput').on('select2:select', function (e) {
        window.aEventHub[self.aid].$emit('change-tags');
      });
    }else{
      $('#atagsinput').tagsinput()
    }
  },
  methods: {
  }
}
</script>
