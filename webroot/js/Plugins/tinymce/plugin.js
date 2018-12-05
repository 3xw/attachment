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
      scope.aEventHub[this.aid].$on('browse-closed', this.openOptions)
      scope.aEventHub[this.aid].$on('upload-closed', this.openOptions)
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

  // Plugin
  var attachment = (function()
  {
    var
    global = tinymce.util.Tools.resolve('tinymce.PluginManager'),
    env = tinymce.util.Tools.resolve('tinymce.Env'),
    conf = tinymce.settings.attachment_settings,
    aid = conf.field

    var Plugin =
    {
      createVueEl: function()
      {
        var component = new Atinymce({propsData: {settings: conf,aid: aid}})
        component.$mount()
        scope.vueTinymce[aid].$el.appendChild(component.$el)
      },
      addEventListener: function(editor)
      {
        scope.aEventHub[aid].$on('options-success', function(options, file){ Plugin.addAttachment(editor, file, options) })
      },
      addAttachment: function(editor, file, options)
      {
        if(options.displayAs == 'Link') editor.insertContent('<a href="'+conf.baseUrl+file.path+'" target="_blank">'+file.path+'</a>')
        else editor.insertContent(Plugin.createImageNode(file, options))
      },
      createImageNode: function(file, options)
      {
        console.log(options)
        console.log(conf)

        var html = '<img'
        var classes = 'uk-responsive-width img-fluid '
        classes += (options.classes)? options.classes+' ': ''
        classes += (options.align)? options.align+' ': ''
        html += ' class=\'' + classes + '\''
        if(options.alt) html += ' alt=\'' + options.alt.replace(/['"]+/g, '') + '\''
        html += ' src=\'' + Plugin.getImagePath(file, options) + '\''
        html += ' />'
        return html
      },
      getImagePath: function(file, options)
      {
        var path = conf.url+'thumbnails/'+file.profile+'/'
        path += (options.width)? 'w'+options.width: 'w1200'
        path += (options.crop)? 'c'+options.cropWidth+'-'+options.cropHeight: '';
        path += '/'+file.path;
        return path;
      },
      addButton: function(editor)
      {
        editor.addButton('attachment',
        {
          type: 'splitbutton',text: 'Média',icon: 'image', menu: [
            {text: 'Télécharger', onclick: function(){scope.aEventHub[aid].$emit('show-upload')}},
            {text: 'Parcourir', onclick: function(){scope.aEventHub[aid].$emit('show-browse')}},
          ]
        })
      },
    }

    // init
    global.add('attachment', function (editor)
    {
      Plugin.createVueEl()
      Plugin.addEventListener(editor)
      Plugin.addButton(editor)
    })

  }())

})(window, Vue)
