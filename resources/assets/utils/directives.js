/* DIRECTIVES
*******************************/
import Vue from 'vue'

Vue.directive('sortable', function (el, binding) {
  options = binding.value || {}
  window.Sortable.create(el, options);
});
