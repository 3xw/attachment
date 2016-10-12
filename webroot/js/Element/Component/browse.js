Vue.component('attachment-browse', {
  template: '#attachment-browse',
  data: function(){
    return {
      search: '',
      tag: '',
      files: [],
      ids: [],
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
    this.getTags().getTypes();
  },
  methods: {
    isSelected: function(id){
      return this.ids.indexOf(id) != -1;
    },
    addEventListeners : function(){
      $('form').bind('submit', this.preventSubmit);
    },
    removeEventListeners : function(){
      $('form').unbind('submit', this.preventSubmit);
    },
    preventSubmit: function(e){
      e.preventDefault();
    },
    close: function(){
      this.removeEventListeners();
      this.files = [];
      this.show = false;
    },
    open: function(){
      this.addEventListeners();
      this.show = true;
      this.getFiles();
      setTimeout(this.setupUI, 500);
    },
    setupUI: function(){
      this.$children[0].setupUI();
    },
    getFiles: function(){
      var params = {page: this.pagination.current_page};

      // add uuid for restriction at search
      params.uuid = this.settings.uuid;

      if(this.search){
        params.search = this.search;
      }
      if(this.tag){
        params.tag = this.tag;
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
      this.files = response.data.data;
      this.pagination = response.data.pagination;
      //console.log(this.pagination);
    },
    getTags: function(){
      var options = {
        headers:{
          "Accept":"application/json",
          "Content-Type":"application/json"
        }
      };
      this.$http.get(this.settings.url+'attachment/atags.json', options)
      .then(this.tagsSuccessCallback, this.errorCallback);
      return this;
    },
    getTypes: function(){
      var options = {
        params: {
          types: '*',
        },
        headers:{
          "Accept":"application/json",
          "Content-Type":"application/json"
        }
      };
      this.$http.get(this.settings.url+'attachment/attachments.json', options)
      .then(this.typesSuccessCallback, this.errorCallback);
      return this;
    },
    tagsSuccessCallback: function(response){
      this.tags = response.data.data;
    },
    typesSuccessCallback: function(response){
      this.types = response.data.data;
    },
    errorCallback: function(response){
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
    }
  }
});
