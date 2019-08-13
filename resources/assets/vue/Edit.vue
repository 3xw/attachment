<template lang="html">
  <div id="attachment-edit" class="modal-mask" v-if="show" transition="modal">
    <div class="modal-wrapper">
      <div class="modal-container container">
        <div class="custom-modal-header">
          Edit
        </div>
        <div class="custom-modal-body">

          <!-- WARNINGS -->
          <div v-for="(error, index) in errors" track-by="$index" class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close"  aria-label="Close" @click="errors = []" ><span aria-hidden="true">&times;</span></button>
            <strong>Watch out!</strong> {{error}}
          </div>

          <!-- loading -->
          <div v-if="loading" class="attachment-loading-container">
            <img v-bind:src="settings.url+'attachment/img/loading.gif'" class="img-responsive" />
          </div>

          <!-- TAGS -->
          <attachment-atags :aid="aid" :file="file" :settings="settings"></attachment-atags>

          <!-- INPUTS -->
          <div v-if="show">

            <!-- IF EMBED -->
            <div v-if="file.type == 'embed'">
              <!-- Name HERE -->
              <div class="input text required">
                <label for="name">Name</label>
                <input v-model="file.name" type="text" name="name" class="form-control attachment-embed__name" id="name" />
              </div>

              <!-- EMBED CODE HERE -->
              <div class="input text required">
                <label for="embed">Embed code</label>
                <textarea v-model="file.embed" name="embed" class="form-control attachment-embed__embed" id="embed" rows="5"></textarea>
              </div>
            </div>

            <!-- IF !EMBED -->
            <div v-if="file.type != 'embed'">
              <div class="input text">
                <label >File</label>
                <p>
                  <!-- file select -->
                  <button v-if="!fileToUpload" @click.prevent="select" role="button" class="btn btn-fill btn-xs btn-info">Change file</button>
                  <input v-show="false" type="file" ref="fileInput" @change="validate($event)" :accept="settings.types.join()" />

                  <!-- file info -->
                  <div v-if="fileToUpload" class="alert alert-info" role="alert">
                    <button type="button" class="close"  aria-label="Close" @click.prevent="select" >
                      <span aria-hidden="true">&times; Change</span>
                    </button>
                    <strong>New file to upload:</strong> {{fileToUpload.name}}
                  </div>
                </p>
              </div>
            </div>

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
                        <input v-model="file.title" type="text" name="title" class="form-control" id="title">
                      </div>
                      <div class="input text">
                        <label for="title">Description</label>
                        <textarea v-model="file.description" name="description" class="form-control" id="description" rows="5"></textarea>
                      </div>
                    </div>

                    <!-- other locales -->
                    <div v-for="(language, index) in settings.i18n.languages"  v-if="language != settings.i18n.defaultLocale" role="tabpanel" class="tab-pane active" :id="'a-'+language">
                      <div class="input text">
                        <label for="title">Title {{language}}</label>
                        <input v-model="file['_translations'][language].title" type="text" :name="'_translations['+language+'][title]'" class="form-control" :id="'a-'+language+'-title'">
                      </div>
                      <div class="input text">
                        <label for="title">Description{{language}}</label>
                        <textarea v-model="file['_translations'][language].description" :name="'_translations['+language+'][description]'" class="form-control" :id="'a-'+language+'-description'" rows="5"></textarea>
                      </div>
                    </div>

                  </div>
                </div>

                <!-- no translate -->
                <div v-if="!settings.i18n.enable" class="col-md-6">
                  <div class="input text">
                    <label for="title">Title</label>
                    <input v-model="file.title" type="text" name="title" class="form-control" id="title">
                  </div>
                  <div class="input text">
                    <label for="title">Description</label>
                    <textarea v-model="file.description" name="description" class="form-control" id="description" rows="5"></textarea>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="input text">
                    <label for="author">Author</label>
                    <input v-model="file.author" type="text" name="author" class="form-control" id="author">
                  </div>
                  <div class="input text">
                    <label for="copyright">Copyright</label>
                    <input v-model="file.copyright" type="text" name="copyright" class="form-control" id="copyright">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <p></p>
        <div class="custom-modal-footer">
          <div class="btn-group">
            <button type="button" class="modal-default-button btn btn-success" @click="edit">
              Edit
            </button>
            <button type="button" class="modal-default-button btn btn-warning" @click="close()">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default
{
  name: 'attachment-edit',
  data: function(){
    return {
      show:false,
      loading: false,
      atags: [],
      file: {},
      fileToUpload: null,
      errors: [],
      success: [] ,
    };
  },
  props: {
    aid:String,
    settings: Object,
  },
  created: function(){
    var instance = this;
    window.aEventHub[this.aid].$on('edit-file',function(file){
      instance.file = file;
      instance.open();
    });
    window.aEventHub[this.aid].$on('change-tags',this.changeTags);
  },
  methods: {
    close: function(){
      this.show = false;
      this.errors = [];
    },
    open: function(){
      this.atags = this.settings.atags;
      this.show = true;
      setTimeout(this.setupUI, 500);
    },
    changeTags: function(){
      this.atags = $('#atagsinput').val();
    },
    setupUI: function(){
      if(this.settings.i18n.enable){
        $('.attachment-locale-area ul a:last').tab('show');
        $('.attachment-locale-area ul a:first').tab('show');
      }
    },
    validate: function(event)
    {
      this.errors = errors = []
      var file = event.target.files[0]

      // test size
      var size = file.size / 1024 / 1024 ;
      if ( !(size > 0) || !(size <= this.settings.maxsize)) {
        errors.push(file.name +  ' ce fichier est trop lourd. La taille max est de ' + this.settings.maxsize + 'MB.')
      }
      // test
      if(this.settings.types.indexOf(file.type) === false || file.type == "" ){
        errors.push(file.name +  ' ce type de fichier n\' est pas supporté.')
      }
      this.errors = errors;
      if(this.errors.length == 0) this.fileToUpload = file
    },
    select: function()
    {
      this.unselect()
      this.$refs.fileInput.click()
    },
    unselect: function()
    {
      this.fileToUpload = null
      this.errors = []
    },
    edit: function()
    {
      this.close();
      window.aEventHub[this.aid].$emit('edit-progress');

      // start gather data
      this.file.uuid = this.settings.uuid;
      delete(this.file.date);
      delete(this.file.created);
      delete(this.file.modified);

      if($('#atagsinput').length > 0){
        var atags = $('#atagsinput').val();
        this.file.atags = [];
        for( var i in atags )
        {
          this.file.atags[i] = {name: atags[i].trim()}
        }
      }

      // if new file...
      if(this.fileToUpload)
      {
        this.file.path = this.fileToUpload;
        delete(this.file.md5);
        delete(this.file.profile);
        delete(this.file.size);
        delete(this.file.type);
        delete(this.file.subtype);
        delete(this.file.name);
        delete(this.file.width);
        delete(this.file.height);
      }

      var options = {headers:{"Accept":"application/json"}};
      var formData = new FormData();
      for( i in this.file ) if(this.file[i] != '' && this.file[i] != null) formData.append(i, this.file[i]);
      for( var t in this.file.atags )
      {
        formData.append('atags['+t+'][name]', this.file.atags[t].name);
      }
      this.$http.post(this.settings.url+'attachment/attachments/edit/'+this.file.id+'.json', formData,options)
      .then(this.editSuccessCallback, this.errorCallback);
      return this;
    },
    editSuccessCallback: function(response){
      window.aEventHub[this.aid].$emit('edit-success', {response:response, file:this.file});
    },
    errorCallback: function(response){
      window.aEventHub[this.aid].$emit('edit-error', {response:response, file:this.file});
    }
  }
}
</script>
