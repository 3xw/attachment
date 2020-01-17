// CSS
require('./css/sass/attachment.scss')

// utils
require('./js/utils.js')
require('./js/filters.js')
require('./js/directives.js')

// init
import AttachmentLoader from './vue/AttachmentLoader.vue'
import VuePackeryPlugin from 'vue-packery-plugin'

Vue.component('AttachmentLoader', AttachmentLoader)
Vue.use(VuePackeryPlugin)
