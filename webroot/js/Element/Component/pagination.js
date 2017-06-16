Vue.component('attachment-pagination',{
  template: '#attachment-pagination',
  props: {
    pagination: {
      type: Object,
      required: true
    },
    lastPage: {
      type: Number,
      default: 1
    },
    from:{type: Number, default: 1},
    to:{type: Number, default: 2},
    callback: {
      type: Function,
      required: true
    },
    offset: {
      type: Number,
      default: 4
    },
    settings: Object,
    start: {
      type: Boolean,
      default: false
    },
    end: {
      type: Boolean,
      default: false
    },
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
