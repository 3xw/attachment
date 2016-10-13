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
    offset: {
      type: Number,
      default: 4
    }
  },
  computed: {
    array: function () {
      if(this.pagination.page_count == 1) {
        return [];
      }

      var from = this.pagination.current_page - this.offset;
      if(from < 1) {
        from = 1;
      }

      var to = from + (this.offset );
      if(to >= this.pagination.page_count) {
        to = this.pagination.page_count;
      }

      var arr = [];
      while (from <=to) {
        arr.push(from);
        from++;
      }

      return arr;
    }
  },
  methods: {
    changePage: function (page) {
      this.pagination.current_page = page;
      this.callback();
    }
  }
});
