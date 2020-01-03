<template lang="html">
  <div id="attachment-filters">
    <div class="card border-0">
      <div class="card-header border-0 bg-white">
        <h4 v-show="$parent.search || $parent.tag || $parent.sort.term">
          <span v-show="$parent.tag">
            <span class="badge badge-primary"><a href="#" @click.prevent="clearTags(), find()"><i class="fa fa-times" style="color:white;" aria-hidden="true"></i></a> Filter: #{{$parent.tag}}</span>
            &nbsp;
          </span>
          <span v-show="$parent.search">
            <span class="badge badge-primary"><a href="#" @click.prevent="clearSearch(null,true), find()"><i class="fa fa-times" style="color:white;" aria-hidden="true"></i></a> Filter: {{$parent.search}} ?</span>
            &nbsp;
          </span>
          <span v-show="$parent.sort.term" class="badge badge-info"><a href="#"><i class="fa fa-times" style="color:white;" aria-hidden="true"></i></a> Order: {{$parent.sort.term}}</span>
        </h4>
      </div>
      <div class="card-body">
        <div class="row no-gutters" >

          <!-- serach -->
          <div class="col">
            <div class="input-group">
              <span class="input-group-addon" id="basic-addon1">?</span>
              <input type="text" class="form-control" id="searchInputSearch" placeholder="file name or title">
            </div>
          </div>

          <!-- SEARCH -->
          <div class="col">
            <button type="button" class="btn btn-fill btn-success" @click.prevent="find()"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
          </div>

          <!-- view mode -->
          <div class="col ml-auto text-right">
            <div class="btn-group">
              <button type="button" class="btn btn-fill btn-secondary" @click.prevent="$parent.listStyle = false">
                <i class="material-icons">view_module</i>
              </button>
              <button type="button" class="btn btn-fill btn-secondary" @click.prevent="$parent.listStyle = true">
                <i class="material-icons">view_list</i>
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
  name: 'attachment-filters',
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
}
</script>
