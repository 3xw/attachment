// CSS
require('./css/sass/attachment.scss')

// utils
require('./js/utils.js')
require('./js/filters.js')
require('./js/directives.js')

// init
import AttachmentLoader from './vue/AttachmentLoader.vue'
import VuePackeryPlugin from 'vue-packery-plugin'

// third party
import 'element-ui/lib/theme-chalk/index.css';
import ElementUI from 'element-ui';

Vue.component('AttachmentLoader', AttachmentLoader)
Vue.use(VuePackeryPlugin)
Vue.use(ElementUI)
