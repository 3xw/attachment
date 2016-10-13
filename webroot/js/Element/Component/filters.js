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
    sortHandler: function(field, name){
      this.$parent.sort.term = name;
      this.$parent.sort.query = {
        sort:field,
        direction:'asc'
      };
    },
    clearTags: function(){
      if($('#tagsInputSearch').val()){
        $('#tagsInputSearch').tagsinput('removeAll');
      }
    },
    clearSearch: function(e, force){
      if( $('#tagsInputSearch').val() != null || force){
        $('#searchInputSearch').val('');
      }
    }
  }
});
