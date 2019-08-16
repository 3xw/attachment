import { defaultMutations} from 'vuex-easy-access'
import atags from './modules/atags.js'
import attachments from './modules/attachments.js'

const state = {
  app: {'*': ''},
  item:
  {
    events: new Vue(),
    attachments: [],
    selection:[],
    settings: {}
  },
  aParams:{
    atags: '',
    search: ''
  },
  tParams:{}
}

export default
{
  namespaced: true,
  state:state,
  actions:
  {
    createChannel({commit}, id)
    {
      commit('app.*', {[id]: Object.assign({},state.item)})
    }
  },
  mutations:
  {
    ...defaultMutations(state),
  },
  modules:
  {
    // mettre les vuex-crud Atag etc...
    atags,
    attachments
  }
}
