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
      align: ['normal','left','center','right'],
      displayAs: ['Image', 'Link'],
      target: ['_blank', '_self']
    },
    selection:{}
  }},
  created: function()
  {
    window.aEventHub[this.aid].$on('show-options', this.showOptions)
  },
  watch:
  {
    file: function (val)
    {
      if(!val) return
      this.selection = {
        displayAs:'Link',
        title: val.title? val.title: val.name,
        width: null,
        crop: 0
      }
    }
  },
  methods:
  {
    showOptions: function()
    {
      this.addEventListeners()
      this.show = true
    },
    addEventListeners : function()
    {
      $(document).bind('keypress', this.preventEnter)
      $('form').bind('submit', this.preventSubmit)
    },
    removeEventListeners : function()
    {
      $(document).unbind('keypress', this.preventEnter)
      $('form').unbind('submit', this.preventSubmit)
    },
    preventEnter: function(e)
    {
      if(e.which == 13) this.preventSubmit(e)
    },
    preventSubmit: function(e)
    {
      e.preventDefault()
      e.stopPropagation()
    },
    close: function()
    {
      this.removeEventListeners()
      this.show = false
    },
    success: function()
    {
      this.close()
      window.aEventHub[this.aid].$emit('options-success', this.selection, this.file)
    }
  }
});
