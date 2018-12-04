Vue.component('attachment-inline-options',{
  template: '#attachment-inline-options',
  props: {
    file: {},
    settings: Object,
    aid: String
  },
  data: function(){ return {
    show: false,
    options: {
      crop: [true,false],
      align: ['left','center','right'],
      displayAs: ['Image','Link','Icon']
    },
    selection:{displayAs:'Link'}
  }},
  created: function()
  {
    window.aEventHub[this.aid].$on('show-options', this.showOptions)
  },
  methods:
  {
    showOptions: function()
    {
      this.show = true
    },
    close: function()
    {
      this.show = false
    },
    success: function()
    {
      this.close()
      window.aEventHub[this.aid].$emit('options-success', [this.options])
    }
  }
});
