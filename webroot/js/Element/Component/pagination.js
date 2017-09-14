Vue.component('attachment-pagination',{
  template: '#attachment-pagination',
  props: {
    pagination: {
      type: Object,
      required: true
    },
    callback: {
      type: Function,
      required: true
    },
    settings: Object,
  },
  data: function(){
    return {
      lastPage: 1,
      offset: 4,
      start: false,
      end: false,
      from: 1,
      to: 2,
    };
  },
  computed: {
    array: function () {
      this.offset = this.settings.pagination.offset;
      this.start = this.settings.pagination.start;
      this.end = this.settings.pagination.end;
      if(this.pagination.page_count == 1) {
        return [];
      }

      this.from = this.pagination.current_page - Math.floor(this.offset/2);
      if(this.from + this.offset >= this.pagination.page_count ){
        this.from = this.pagination.current_page - (this.offset - (this.pagination.page_count - this.pagination.current_page));
      }
      if(this.from < 1) {
        this.from = 1;
      }

      this.to = this.from + (this.offset );
      if(this.to >= this.pagination.page_count) {
        this.to = this.pagination.page_count;
      }

      var arr = [];
      var i = this.from;
      while (i <=this.to) {
        arr.push(i);
        i++;
      }

      return arr;
    }
  },
  methods: {
    changePage: function (page) {
      this.lastPage = this.pagination.current_page;
      this.pagination.current_page = page;
      this.callback();
    }
  }
});
