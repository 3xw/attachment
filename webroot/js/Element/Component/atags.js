Vue.component('attachment-atags',{
  template: '#attachment-atags',
  props: {
    settings: {
      type: Object,
      required: true,
      twoWay: true
    }
  },
  data: function(){
    return {}
  },
  created: function()
  {
    if(this.settings.restrictions.indexOf('tag_restricted') == -1 || this.settings.restrictions.indexOf('tag_or_restricted') != -1)
    {
      $('#atagsinput').tagsinput()
    }
  },
  methods: {
  }
});
