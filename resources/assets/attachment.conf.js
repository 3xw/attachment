// CSS
require('./css/sass/attachment.scss')

// utils
require('./js/filters.js')
require('./js/directives.js')

// init
import AttachmentLoader from './vue/AttachmentLoader.vue'
Vue.component('AttachmentLoader', AttachmentLoader)

// store
import attachment from './js/store/modules/attachment.js'
