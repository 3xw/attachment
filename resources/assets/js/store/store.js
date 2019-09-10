import { defaultMutations} from 'vuex-easy-access'
import atags from './modules/atags.js'
import attachments from './modules/attachments.js'

let state = function(){
  return {
    aParams:{
      uuid: '',
      atags: '',
      search: '',
      page: 1,
    },
    tParams:{
      uuid: '',
    }
  }
}

export default
{
  namespaced: true,
  state:state,
  mutations:
  {
    'aParams.atags': (state, payload) => {
          state.aParams.atags = payload
        }
  },
  modules:
  {
    // mettre les vuex-crud Atag etc...
    atags,
    attachments
  }
}
