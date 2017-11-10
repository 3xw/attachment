Vue.component('attachment-input',{
  template: '#attachment-input',
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
});
