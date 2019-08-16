import { defaultMutations} from 'vuex-easy-access'
import atags from './atags.js'
import attachments from './attachments.js'

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
    atags: ''
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
    },
    setAParms({}, )
    {

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
