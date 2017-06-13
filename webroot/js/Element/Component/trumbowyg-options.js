Vue.component('attachment-trumbowyg-options',{
  template: '#attachment-trumbowyg-options',
  props: {
    settings: Object,
    file: {},
    show: {
      type: Boolean,
      default: false
    },
  },
  data: function(){
    return {
      options: {
        crop: false,
        align: ''
      }
    }
  },
  events: {
    'show-options': function(){
      this.show = true;
    }
  },
  methods: {
    close: function(){
      this.show = false;
    },
    success: function(){
      this.$dispatch('options-success', [this.options]);
      this.close();
    }
  }
});
