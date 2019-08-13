<template lang="html">
  <div id="attachment-browse" class="modal-mask" v-if="show" transition="modal">
    <div class="modal-wrapper">
      <div class="modal-container container">
        <div class="custom-modal-header">
          Browse files
        </div>
        <div class="custom-modal-body">

          <!-- filters -->
          <div class="btn-group float-right">
            <button type="button" class="modal-default-button btn btn-fill btn-warning" @click="close()">
              Close
            </button>
          </div>
          <attachment-filters :types="types" :tags="tags" :callback="getFiles" ></attachment-filters>

          <!-- WARNINGS -->
          <div v-for="(error, index) in errors" track-by="$index" class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close"  aria-label="Close" @click="errors = []" ><span aria-hidden="true">&times;</span></button>
            <strong>Watch out!</strong> {{error}}
          </div>

          <!-- loading -->
          <div v-if="loading" class="attachment-loading-container">
            <img v-bind:src="this.settings.url+'attachment/img/loading.gif'" class="img-responsive" />
          </div>

          <!-- file list -->
          <div v-if="!loading" class="row">

            <!-- list option -->
            <div class="col-12">
              <table v-if="listStyle" class="table table-bordered table-striped table-condensed table-hover">
                <!-- row -->
                <tr>
                  <th></th>
                  <th>Name</th>
                  <th>Size</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
                <tr v-for="(file, index) in files" :id="'file-'+index">
                  <td>
                    <span v-html="$options.filters.icon(file.type+'/'+file.subtype)"></span>
                  </td>
                  <td>
                    <span v-if="file.title">
                      <b>{{file.title}}</b><br/>
                    </span>
                    {{file.name}}
                  </td>
                  <td>
                    {{file.size | bytesToMegaBytes | decimal(2) }} MB
                  </td>
                  <td>
                    {{file.created}}
                  </td>
                  <td>
                    <div class="btn-group">
                      <!-- data -->
                      <a v-if="from == 'input'" v-show="!isSelected(file.id)" href="#" class="btn btn-xs btn-fill btn-info" role="button" @click="add(index);"  >Add</a>
                      <a v-if="from != 'input'" v-show="!isSelected(file.id)" href="#" class="btn btn-xs btn-fill btn-info" role="button" @click="trumbAdd(file);"  >Add</a>

                      <a v-show="isSelected(file.id)" href="#" class="btn btn-xs btn-fill btn-warning" role="button" @click="remove(file.id)" >Remove</a>
                    </div>
                  </td>

                </tr>
              </table>
            </div>

            <!-- thumb option -->
            <div v-if="!listStyle"  v-for="(file, index) in files" :id="index"  class="attachment-files__item col-6 col-sm-4 col-md-3 col-lg-2">
              <div class="card mb-4" >

                <!-- thumb -->
                <attachment-thumb :url="settings.url" :file="file"></attachment-thumb>

                <div class="card-body">
                  <p class="card-text small" style="overflow:auto;">
                    <span v-if="file.title">{{file.title}}</span>
                    <span v-else>{{file.name}}</span>
                    <br/>
                    {{file.size | bytesToMegaBytes | decimal(2) }} MB<br/>
                  </p>

                  <!-- data -->

                  <a v-if="from == 'input'" v-show="!isSelected(file.id)" href="#" class="btn btn-xs btn-fill btn-info" role="button" @click="add(index);"  >Add</a>
                  <a v-if="from != 'input'" v-show="!isSelected(file.id)" href="#" class="btn btn-xs btn-fill btn-info" role="button" @click="trumbAdd(file);"  >Add</a>

                  <a v-show="isSelected(file.id)" href="#" class="btn btn-xs btn-fill btn-warning" role="button" @click="remove(file.id)" >Remove</a>

                </div>
              </div><!-- end card -->
            </div>
          </div>

          <!-- pagination -->
          <attachment-pagination :pagination="pagination" :callback="getFiles" :settings.sync="settings"></attachment-pagination>

          <p></p>
          <div class="custom-modal-footer">
            <div class="btn-group">
              <button type="button" class="modal-default-button btn btn-fill btn-warning" @click="close()">
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
  name: 'attachment-browse',
  data: function(){
    return {
      search: '',
      tag: '',
      sort: { query: '', term: ''},
      errors: [],
      files: [],
      ids: [],
      loading: true,
      show: false,
      tags: [],
      types: [],
      listStyle: false,
      pagination: {
        "page_count": 1,
        "current_page": 1,
        "has_next_page": false,
        "has_prev_page": false,
        "count": 0,
        "limit": 100
      }
    };
  },
  props: {
    aid: String,
    settings: Object,
    from: String,
  },
  created: function(){

    for(var i in this.settings.attachments){
      this.files.push(this.settings.attachments[i]);
      this.ids.push(this.settings.attachments[i].id);
    }
    window.aEventHub[this.aid].$on('show-browse', this.browseOpen);
    window.aEventHub[this.aid].$on('add-file-id', this.addId);
    window.aEventHub[this.aid].$on('remove-file-id', this.removeId);
  },
  mounted: function(){
    this.getTags();
    this.types = this.settings.types;
  },
  methods: {
    isSelected: function(id){
      return this.ids.indexOf(id) != -1;
    },
    browseOpen: function(){
      this.open();
    },
    trumbAdd: function(file){
      this.settings.attachments.push(file);
      this.close()
    },
    addId: function(id){
      if(this.settings.relation == 'belongsTo'){
        this.ids = [];
      }else{
        if(this.settings.maxquantity != -1 && (this.ids.length == this.settings.maxquantity)){
          this.ids.shift();
        }
      }
      this.ids.push(id);
    },
    removeId: function(id){
      var index = this.ids.indexOf(id);
      if(index != -1){
        this.ids.splice(index,1);
      }
    },
    addEventListeners : function(){
      $(document).bind('keypress', this.preventEnter);
      $('form').bind('submit', this.preventSubmit);
    },
    removeEventListeners : function(){
      $(document).unbind('keypress', this.preventEnter);
      $('form').unbind('submit', this.preventSubmit);
    },
    preventEnter: function(e){
      if(e.which == 13) {
        this.preventSubmit(e);
      }
    },
    preventSubmit: function(e){
      e.preventDefault();
      e.stopPropagation();
    },
    close: function(){
      this.removeEventListeners();
      this.files = [];
      this.show = false;
      this.loading = false;
      window.aEventHub[this.aid].$emit('browse-closed');
    },
    open: function(){
      this.addEventListeners();
      this.loading = true;
      this.show = true;
      this.getFiles();
      setTimeout(this.setupUI, 500);
    },
    setupUI: function(){
      this.$children[0].setupUI();
    },
    getFiles: function(){
      this.loading = true;
      var params = {page: this.pagination.current_page};

      // add uuid for restrictions
      params.uuid = this.settings.uuid;

      if(this.search){
        params.search = this.search;
      }
      if(this.tag){
        params.tag = this.tag;
      }
      if(this.sort.term){
        params.sort = this.sort.query.sort;
        params.direction = this.sort.query.direction;
      }

      if(!this.tag && !this.search){
        params.index = 'filter';
      }

      var options = {
        params: params,
        headers:{
          "Accept":"application/json",
          "Content-Type":"application/json"
        }
      };
      this.$http.get(this.settings.url+'attachment/attachments.json', options)
      .then(this.successCallback, this.errorCallback);
    },
    successCallback: function(response){
      this.loading = false;
      this.files = response.data.data;
      this.pagination = response.data.pagination;
    },
    getTags: function(){
      var options = {
        params: {index:'filter'},
        headers:{
          "Accept":"application/json",
          "Content-Type":"application/json"
        }
      };
      this.$http.get(this.settings.url+'attachment/atags.json', options)
      .then(this.tagsSuccessCallback, this.errorCallback);
      return this;
    },
    tagsSuccessCallback: function(response){
      this.tags = response.data.data;
    },
    errorCallback: function(response){
      this.loading = false;
      var message = ( response.data )? response.data.data.message : 'Your session is lost, please login again!';
      this.errors.push(message);
      console.log(response);
    },
    add: function(index){
      window.aEventHub[this.aid].$emit('add-file', this.files[index]);
    },
    remove: function(id){
      window.aEventHub[this.aid].$emit('remove-file', id);
    }
  }
}
</script>
