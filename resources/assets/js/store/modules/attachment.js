import { defaultMutations} from 'vuex-easy-access'

// do the magic 🧙🏻‍♂️
const state = {
  app: {'*': ''},
  item:
  {
    eventHub: new Vue(),
    attachments: [],
    settings: {}
  },
  count: 0
}

export default
{
  namespaced: true,
  state:state,
  actions:
  {
    init({commit}, id)
    {
      commit('app.*', {id.id: Object.assign({},state.item)})
    }
  },
  mutations:
  {
    // do the magic 🧙🏻‍♂️
    ...defaultMutations(state),
  },
  modules:
  {
    // mettre les vuex-crud Atag etc...
  }
}
