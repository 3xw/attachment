export default
{
  namespaced: true,
  state()
  {
    return {
      aParams:{
        uuid: '',
        atags: '',
        search: '',
        page: 1,
      },
      tParams:{
        uuid: '',
      },
      settings: {}
    }
  },
  mutations:
  {
    'aParams': (state, payload) => {
      state.aParams = payload
    },
    'settings': (state, payload) => {
      state.settings = payload
    }
  },
}
