/*
 *
 * 💀💀
 *
 ********/
Vue.component('attachment-browse', {
  template: '#attachment-browse',
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
});
