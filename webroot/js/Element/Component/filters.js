Vue.component('attachment-filters',{
  template: '#attachment-filters',
  props: {
    tags: Array,
    types: Array,
    callback: {
      type: Function,
      required: true
    }
  },
  data: function(){
    return {
      sort: { query: '', term: ''}
    }
  },
  methods: {
    find: function(){
      this.$parent.pagination.current_page = 1;
      this.$parent.search = $('#searchInputSearch').val() || null;
      this.$parent.tag = $('#tagsInputSearch').val() || null;
      this.callback();
    },
    setupUI: function(){

      var tagsList = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        local: this.tags
      });
      tagsList.initialize();

      $('#tagsInputSearch').tagsinput({
        freeInput: true,
        itemValue: 'name',
        itemText: 'name',
        typeaheadjs: {
            name: 'tagsList',
            displayKey: 'name',
            source: tagsList.ttAdapter()
        }
      });

      $('#tagsInputSearch').change(this.clearSearch);
      $('#searchInputSearch').keyup(this.clearTags);

    },
    clearTags: function(){
      console.log('clearTags');
      if($('#tagsInputSearch').val()){
        $('#tagsInputSearch').tagsinput('removeAll');
      }
    },
    clearSearch: function(e, force){
      console.log('clearSearch');
      console.log($('#tagsInputSearch').val());
      console.log(force);
      if( $('#tagsInputSearch').val() != null || force){
        $('#searchInputSearch').val('');
      }
    }
  }
});
