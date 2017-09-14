Vue.component('attachment-embed', {
  template: '#attachment-embed',
  data: function(){
    return {
      atags: [],
      errors: [],
      success: [] ,
      show: false,
    };
  },
  props: {
    settings: Object,
  },
  created: function(){
    window.aEventHub.$on('show-embed', this.showEmbed);
  },
  methods: {
    showEmbed: function(){
      this.open();
    },
    close: function(){
      this.show = false;
      this.errors = [];
    },
    open: function(){
      this.atags = this.settings.atags;
      this.show = true;
      setTimeout(this.setupUI, 500);
    },
    setupUI: function(){
      if(this.settings.restrictions.indexOf('tag_restricted') == -1){
        $('#atagsinput').tagsinput();
      }
      if(this.settings.i18n.enable){
        $('.attachment-locale-area ul a:last').tab('show');
        $('.attachment-locale-area ul a:first').tab('show');
      }
    },
    upload: function(){

      // check if name
      if($('.attachment-embed__name').val() == ''){
        this.errors.push('Filed name is required!');
        return;
      }

      // check if embed
      if($('.attachment-embed__embed').val() == ''){
        this.errors.push('No embed code filed is required!');
        return;
      }

      var self = this;
      var formData = new FormData();
      formData.append('name', $('.attachment-embed__name').val().trim());
      formData.append('embed', $('.attachment-embed__embed').val().trim());
      formData.append('uuid', this.settings.uuid);

      // retrieve tags
      var tags = (this.settings.restrictions.indexOf('tag_restricted') == -1)? $('#atagsinput').val(): this.atags;
      for( var t in tags )
      {
        formData.append('atags['+t+'][name]', tags[t].trim());
      }
      $('.optional-fields input, .optional-fields textarea, .optional-fields select').each(function(){
        var $input = $(this);
        var value = $input.val();
        if(value)
        {
            formData.append($input.attr('name'), value.trim());
        }
      });

      $.ajax({
        type: 'POST',
        dataType: 'json',
        url: this.settings.url+'attachment/attachments/add.json',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(response){
          if(self.settings.relation != 'belongsToMany'){
            if(self.settings.attachments.length > 0){
              self.settings.attachments.pop();
            }
          }
          //self.settings.attachments.push(response.data);
          window.aEventHub.$emit('add-file', response.data);
          self.close();
          window.aEventHub.$emit('embed-finished');
        },
        error: function(response){
          var message = response.statusText;

          //self.success = '';
          if(response.responseJSON){
            var errors = ( response.responseJSON['data'] )? response.responseJSON.data.errors: response.responseJSON.errors;
            var message = ( response.responseJSON['data'] )? response.responseJSON.data.message: response.responseJSON.message;

            if(errors){
              if(errors['md5']){
                if(errors['md5']['unique']){
                  message = errors['md5'].unique;
                }
              }
            }
          }
          self.errors.push(message);
        }
      });
    }
  }
});
