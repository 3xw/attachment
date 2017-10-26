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
    tags: Array,
    types: Array,
    settings: Object,
    show: {
      type: Boolean,
      default: false
    },
  },
  events: {
    'show-browse': function(){
      this.open();
    }
  },
  watch: {
    'settings.attachments' : function(val, oldVal){
      var newIds = [];
      for(var i in val ){
        newIds.push(val[i].id);
      }
      this.ids = newIds;
    }
  },
  ready: function(){
    for(var i in this.settings.attachments ){
      this.ids.push(this.settings.attachments[i].id);
    }
    this.getTags();
    this.types = this.settings.types;
  },
  methods: {
    isSelected: function(id){
      return this.ids.indexOf(id) != -1;
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
      this.$dispatch('browse-closed');
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
    },
    errorCallback: function(response){
      this.loading = false;
      var message = ( response.data )? response.data.data.message : 'Your session is lost, please login again!';
      this.errors.push(message);
      console.log(response);
    },
    add: function(index){
      if(this.settings.relation != 'belongsToMany'){
        if(this.settings.attachments.length > 0){
          this.settings.attachments.pop();
        }
      }
      this.settings.attachments.push(this.files[index]);
    },
    remove: function(id){
      var index = this.ids.indexOf(id);
      this.settings.attachments.splice(index,1);
    },
    getFullLink: function(index){
      console.log(this.settings.attachments);
      var file = this.files[index];
      var fullUrl = this.settings.baseUrls[file.profile]+file.path;
      this.showLink(fullUrl);
    },
    showLink: function(val){
      $('#attachment-full-url').remove();
      $('#attachment-browse .row').after('<div id="attachment-full-url"><label>Attachment Full URL</label><input type="text" class="form-control" value="'+val+'"/></a></div>');
    }
  }
});
