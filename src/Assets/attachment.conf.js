// CSS
require('./css/sass/attachment.scss')

/* EVENT HUB
*******************************/
window.aEventHub = {};//new Vue();

//JS
require('./js/filters.js');
require('./js/directives.js');

import AttachmentLoader from './vue/AttachmentLoader.vue';

Vue.component('AttachmentLoader', AttachmentLoader);
