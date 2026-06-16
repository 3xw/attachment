Vue.component('attachment-link', {
  template: '#attachment-link',
  data: function(){
    return {
      atags: [],
      errors: [],
      show: false,
    };
  },
  props: {
    aid: String,
    settings: Object,
  },
  created: function(){
    window.aEventHub[this.aid].$on('show-link', this.showLink);
    window.aEventHub[this.aid].$on('change-tags', this.changeTags);
  },
  methods: {
    showLink: function(){
      this.open();
    },
    changeTags: function(){
      this.atags = $('#atagsinput').val();
    },
    close: function(){
      this.show = false;
      this.errors = [];
    },
    open: function(){
      this.atags = this.settings.atags;
      this.show = true;
    },
    upload: function(){
      if($('.attachment-link__name').val() == ''){
        this.errors.push('Field name is required!');
        return;
      }
      if($('.attachment-link__url').val() == ''){
        this.errors.push('Field URL is required!');
        return;
      }

      var self = this;
      var formData = new FormData();
      formData.append('name', $('.attachment-link__name').val().trim());
      formData.append('link', $('.attachment-link__url').val().trim());
      formData.append('uuid', this.settings.uuid);

      var tags = (this.settings.atagsDisplay == 'input')? $('#atagsinput').val(): this.atags;
      for( var t in tags ){
        formData.append('atags['+t+'][name]', tags[t].trim());
      }
      $('.optional-fields input, .optional-fields textarea, .optional-fields select').each(function(){
        var $input = $(this);
        formData.append($input.attr('name'), $input.val().trim());
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
          }else{
            if(self.settings.maxquantity != -1 && (self.settings.attachments.length == self.settings.maxquantity)){
              self.settings.attachments.shift();
            }
          }
          window.aEventHub[self.aid].$emit('add-file', response.data);
          self.close();
        },
        error: function(response){
          var message = response.statusText;
          if(response.responseJSON){
            var errors = (response.responseJSON['data'])? response.responseJSON.data.errors : response.responseJSON.errors;
            message = (response.responseJSON['data'])? response.responseJSON.data.message : response.responseJSON.message;
            if(errors && errors['md5'] && errors['md5']['unique']){
              message = errors['md5'].unique;
            }
          }
          self.errors.push(message);
        }
      });
    }
  }
});
