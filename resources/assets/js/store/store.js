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
    'aParams.atags': (state, payload) => {
      state.aParams.atags = payload
    },
    'settings': (state, payload) => {
      state.settings = payload
    }
  },
}
