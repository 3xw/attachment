export default
{
  namespaced: true,
  state()
  {
    return {
      upload: {
        tags: [],
        files: []
      },
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
    'upload': (state, payload) => {
      state.upload = payload
    },
    'aParams': (state, payload) => {
      state.aParams = payload
    },
    'settings': (state, payload) => {
      state.settings = payload
    }
  },
}
