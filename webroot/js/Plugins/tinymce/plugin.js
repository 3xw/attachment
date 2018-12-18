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
    Plugin =
    {
      createVueEl: function(editor)
      {
        var conf = editor.settings.attachment_settings,
        aid = conf.field,
        component = new Atinymce({propsData: {settings: conf,aid: aid}})

        component.$mount()
        scope.vueTinymce[aid].$el.appendChild(component.$el)
      },
      addEventListener: function(editor)
      {
        var conf = editor.settings.attachment_settings,
        aid = conf.field

        scope.aEventHub[aid].$on('options-success', function(options, file){ Plugin.addAttachment(editor, file, options) })
      },
      addAttachment: function(editor, file, options)
      {
        var conf = editor.settings.attachment_settings,
        aid = conf.field
        console.log(conf);

        if(options.displayAs == 'Link') editor.insertContent('<a href="'+file.fullpath+'" target="_blank">'+options.title+'</a>')
        else editor.insertContent(Plugin.createImageNode(conf, file, options))
      },
      createImageNode: function(conf, file, options)
      {
        var html = '<img'
        var classes = 'uk-responsive-width img-fluid '
        classes += (options.classes)? options.classes+' ': ''
        classes += (options.align)? options.align+' ': ''
        html += ' class=\'' + classes + '\''
        if(options.alt) html += ' alt=\'' + options.alt.replace(/['"]+/g, '') + '\''
        html += ' src=\'' + Plugin.getImagePath(conf, file, options) + '\''
        html += ' />'
        return html
      },
      getImagePath: function(conf, file, options)
      {
        var path = conf.url+'thumbnails/'+file.profile+'/'
        path += (options.width)? 'w'+options.width: 'w1200'
        path += (options.crop)? 'c'+options.cropWidth+'-'+options.cropHeight: '';
        path += '/'+file.path;
        return path;
      },
      addButton: function(editor)
      {
        var conf = editor.settings.attachment_settings,
        aid = conf.field

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
      Plugin.createVueEl(editor)
      Plugin.addEventListener(editor)
      Plugin.addButton(editor)
    })

  }())

})(window, Vue)
