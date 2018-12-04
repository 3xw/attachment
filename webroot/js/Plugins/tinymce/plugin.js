(function (scope, Vue)
{
  // Atinymce component
  var Atinymce = Vue.component('attachment-tinymce', {
    template: '<div><small><i>packed with attachment</i> 🤓</small>'
    +'<attachment-upload :aid="aid" :settings.sync="settings" ></attachment-upload>'
    +'<attachment-browse :aid="aid" :types="settings.types" :tags="settings.atags" :settings="settings" from="tinymce"></attachment-browse>'
    +'<attachment-inline-options :aid="aid" :file="file" :settings="settings"></attachment-inline-options>'
    +'</div>',
    props: {settings:Object,aid:String},
    data: function(){ return {file:null} },
    created: function()
    {
      scope.aEventHub[this.aid] = new Vue()
      window.aEventHub[this.aid].$on('browse-closed', this.openOptions)
      window.aEventHub[this.aid].$on('upload-closed', this.openOptions)
    },
    methods:
    {
      openOptions: function()
      {
        this.file = this.settings.attachments.shift()
        if(this.file) scope.aEventHub[this.aid].$emit('show-options')
      },
    }
  })

  // plugin...
  var attachment = (function()
  {
    var
    global = tinymce.util.Tools.resolve('tinymce.PluginManager'),
    env = tinymce.util.Tools.resolve('tinymce.Env'),
    conf = tinymce.settings.attachment_settings,
    aid = conf.field

    // log stuff...
    console.log(conf)

    // create vuejs component
    var component = new Atinymce({propsData: {settings: conf,aid: aid}})
    component.$mount()
    scope.vueTinymce[aid].$el.appendChild(component.$el)

    // create button and bin functions
    global.add('attachment', function (editor)
    {
      editor.addButton('attachment',
      {
        type: 'splitbutton',text: 'Média',icon: 'image', menu: [
          {text: 'Télécharger', onclick: function(){scope.aEventHub[aid].$emit('show-upload')}},
          {text: 'Parcourir', onclick: function(){scope.aEventHub[aid].$emit('show-browse')}},
        ]
      })
    })

  }())

})(window, Vue)
