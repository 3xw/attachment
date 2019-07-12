<template lang="html">
  <div id="attachment-embed" class="modal-mask" v-if="show" transition="modal">
    <div class="modal-wrapper">
      <div class="modal-container container">
        <div class="custom-modal-header">
          Add an embed code
        </div>
        <div class="custom-modal-body">

          <!-- WARNINGS -->
          <div v-for="(error, index) in errors" track-by="$index" class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close"  aria-label="Close" @click="errors = []" ><span aria-hidden="true">&times;</span></button>
            <strong>Watch out</strong> {{error}}
          </div>

          <!-- TAGS -->
          <attachment-atags :aid="aid" :settings="settings"></attachment-atags>

          <!-- INPUTS -->
          <div v-if="show">

            <!-- OPTIONS -->
            <div  id="attachment-options">
              <div class="row optional-fields">

                <!-- _translations[en_GB][name] -->
                <div v-if="settings.i18n.enable" class="col-md-6 attachment-locale-area">
                  <ul class="nav nav-tabs" role="tablist">
                    <li v-for="(language, index) in settings.i18n.languages" v-bind:class="{ 'active': language ==  settings.i18n.defaultLocale}" role="presentation">
                      <a :href="'#a-'+language" :aria-controls="'a-'+language" role="tab" data-toggle="tab" >
                        {{language}}
                      </a>
                    </li>
                  </ul>
                  <div class="tab-content">

                    <!-- defaultLocale -->
                    <div role="tabpanel" class="tab-pane active" :id="'a-'+settings.i18n.defaultLocale">
                      <div class="input text">
                        <label for="title">Title</label>
                        <input type="text" name="title" class="form-control" id="title">
                      </div>
                      <div class="input text">
                        <label for="title">Description</label>
                        <textarea name="description" class="form-control" id="description" rows="5"></textarea>
                      </div>
                    </div>

                    <!-- other locales -->
                    <div v-for="(language, index) in settings.i18n.languages"  v-if="language != settings.i18n.defaultLocale" role="tabpanel" class="tab-pane active" :id="'a-'+language">
                      <div class="input text">
                        <label for="title">Title{{language}}</label>
                        <input type="text" :name="'_translations['+language+'][title]'" class="form-control" :id="'a-'+language+'-title'">
                      </div>
                      <div class="input text">
                        <label for="title">Description {{language}}</label>
                        <textarea :name="'_translations['+language+'][description]'" class="form-control" :id="'a-'+language+'-description'" rows="5"></textarea>
                      </div>
                    </div>

                  </div>
                </div>

                <!-- no translate -->
                <div v-if="!settings.i18n.enable" class="col-md-6">
                  <div class="input text">
                    <label for="title">Title</label>
                    <input type="text" name="title" class="form-control" id="title">
                  </div>
                  <div class="input text">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control" id="description" rows="5"></textarea>
                  </div>
                </div>


                <div class="col-md-6">
                  <div class="input text">
                    <label for="author">Author</label>
                    <input type="text" name="author" class="form-control" id="author">
                  </div>
                  <div class="input text">
                    <label for="copyright">Copyright</label>
                    <input type="text" name="copyright" class="form-control" id="copyright">
                  </div>
                </div>
              </div>
            </div>

            <!-- Name HERE -->
            <div class="input text required">
              <label for="name">Name</label>
              <input type="text" name="name" class="form-control attachment-embed__name" id="name" />
            </div>

            <!-- EMBED CODE HERE -->
            <div class="input text required">
              <label for="embed">Embed code</label>
              <textarea name="embed" class="form-control attachment-embed__embed" id="embed" rows="5"></textarea>
            </div>

          </div>
          <p></p>
          <div class="custom-modal-footer">
            <div class="btn-group">
              <button type="button" class="modal-default-button btn btn-success" @click="upload">
                Upload
              </button>
              <button type="button" class="modal-default-button btn btn-warning" @click="close()">
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default
{
  name: 'attachment-embed',
  data: function(){
    return {
      atags: [],
      errors: [],
      success: [] ,
      show: false,
    };
  },
  props: {
    aid:String,
    settings: Object,
  },
  created: function(){
    window.aEventHub[this.aid].$on('show-embed', this.showEmbed);
    window.aEventHub[this.aid].$on('change-tags',this.changeTags);
  },
  methods: {
    showEmbed: function(){
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
      setTimeout(this.setupUI, 500);
    },
    setupUI: function(){
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
      var tags = (this.settings.atagsDisplay == 'input')? $('#atagsinput').val(): this.atags;
      for( var t in tags )
      {
        formData.append('atags['+t+'][name]', tags[t].trim());
      }
      $('.optional-fields input, .optional-fields textarea, .optional-fields select').each(function(){
        var $input = $(this);
        var value = $input.val();
        formData.append($input.attr('name'), value.trim());
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
          //self.settings.attachments.push(response.data);
          window.aEventHub[self.aid].$emit('add-file', response.data);
          self.close();
          window.aEventHub[self.aid].$emit('embed-finished');
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
}
</script>
