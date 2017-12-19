Vue.component('attachment-index',{
  template: '#attachment-index',
  props: {
    settings: Object,
    aid:String,
    //files: Array,
  },
  data:function(){
    return {
      tags: [],
      types: [],
      search: '',
      tag: '',
      sort: { query: '', term: ''},
      successes: [],
      errors: [],
      files: [],
      fileToDeleteName: '',
      ids: [],
      loading: true,
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
  mounted: function(){
    this.types = this.settings.types;
    this.getTags();
    this.getFiles();
  },
  watch: {
    'loading' : function(val, oldVal){
      if(val === true){
        this.files = [];
      }
    }
  },
  created: function(){
    if(window.aEventHub[this.aid] == undefined){
      window.aEventHub[this.aid] = new Vue();
      console.log('index',this.aid);
    }

    window.aEventHub[this.aid].$on('show-edit-file', this.showEditFile);
    window.aEventHub[this.aid].$on('show-view-file', this.showViewFile);
    window.aEventHub[this.aid].$on('edit-progress', this.editProgress);
    window.aEventHub[this.aid].$on('edit-success', this.editSuccess);
    window.aEventHub[this.aid].$on('edit-error', this.editError);
    window.aEventHub[this.aid].$on('upload-finished', this.getFiles);
    window.aEventHub[this.aid].$on('embed-finished', this.getFiles);
    window.aEventHub[this.aid].$on('delete-file', this.deleteFile);
  },
  methods: {
    showEditFile:function(index) {
      window.aEventHub[this.aid].$emit('edit-file',this.files[index]);
    },
    showViewFile:function(index) {
      window.aEventHub[this.aid].$emit('view-file',this.files[index]);
    },
    editProgress: function(){
      this.loading = true;
    },
    editSuccess: function(data){
      this.loading = false;
      this.successes.push('file: '+data.file.name+' successfully edited!');
      this.getTags();
      this.getFiles();
    },
    editError: function(data){
      this.loading = false;
      var message = ( data.response.data && data.response.data.data && data.response.data.data.message )? 'file: '+data.file.name+' '+data.response.data.data.message : 'Your session is lost, please login again!';
      if( data.response.data && data.response.data.message ){
        message = 'file: '+data.file.name+' '+data.response.data.message;
      }
      this.errors.push(message);
      console.log(data.response);
      this.getFiles();
    },
    deleteFile: function(index){
      console.log(index);
      var file = this.files[index];
      console.log(file);
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
      .then(this.deleteSuccessCallback, this.errorDeleteCallback);
    },
    dispatch(evt,aid,data){
      console.log('allo');
      console.log(evt, aid, data);
      window.aEventHub[aid].$emit(evt, data);
    },
    getFileByIndex: function(index){
      return this.files[index];
    },
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
      //this.settings.atags = response.data.data;
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
    errorDeleteCallback: function(response){
      this.loading = false;
      var message = ( response.data )? response.data.data.message : 'Your session is lost, please login again!';
      this.errors.push(message);
      console.log(response);
      this.getFiles();
    },
  }
});
