Vue.component('attachment-index',{
  template: '#attachment-index',
  props: {
    tags: Array,
    types: Array,
    settings: Object,
    //files: Array,
  },
  data:function(){
    return {
      search: '',
      tag: '',
      sort: { query: '', term: ''},
      successes: [],
      errors: [],
      files: [],
      fileToDeleteName: '',
      ids: [],
      loading: true,
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
  ready: function(){
    this.types = this.settings.types;
    this.getTags();
    this.getFiles();
  },
  events: {
    'show-edit-file': function(index) {
      this.$broadcast('edit-file',this.files[index]);
    },
    'edit-progress': function(){
      this.loading = true;
    },
    'edit-success': function(response, file){
      this.loading = false;
      this.successes.push('file: '+file.name+' successfully edited!');
      this.getTags();
    },
    'edit-error': function(response, file){
      this.loading = false;
      var message = ( response.data && response.data.data && response.data.data.message )? 'file: '+file.name+' '+response.data.data.message : 'Your session is lost, please login again!';
      if( response.data && response.data.message ){
        message = 'file: '+file.name+' '+response.data.message;
      }
      this.errors.push(message);
      console.log(response);
    },
    'delete-file': function(index){
      var file = this.files[index];
      this.fileToDeleteName = file.name;
      if(!confirm('Delete file: '+this.fileToDeleteName+'?')){
        return false;
      }
      var options = {
        headers:{
          "Accept":"application/json",
          "Content-Type":"application/json"
        }
      };
      file.uuid = this.settings.uuid;
      this.loading = true;
      this.$http.delete(this.settings.url+'attachment/attachments/delete/'+file.id+'.json', file,options)
      .then(this.deleteSuccessCallback, this.errorCallback);
    },
    'upload-finished': function(){
      this.getFiles();
    },
    'embed-finished': function(){
      this.getFiles();
    },
  },
  methods: {
    dispalyEmbed : function(){
      for( var type in this.settings.types ){
        if(this.settings.types[type].indexOf('embed') != -1)
        {
          return true;
        }
      }
      return false;
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
      .then(this.filesSuccessCallback, this.errorCallback);
    },
    filesSuccessCallback: function(response){
      this.loading = false;
      this.files = response.data.data;
      this.pagination = response.data.pagination;
      //console.log(this.pagination);
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
      this.settings.atags = response.data.data;
    },
    deleteSuccessCallback: function(response){
      this.loading = false;
      this.successes.push('file: '+this.fileToDeleteName+' was successfully deleted!');
      this.getFiles();
    },
    errorCallback: function(response){
      this.loading = false;
      var message = ( response.data )? response.data.data.message : 'Your session is lost, please login again!';
      this.errors.push(message);
      console.log(response);
    },
  }
});
