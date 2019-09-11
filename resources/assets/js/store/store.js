export default
{
  namespaced: true,
  state()
  {
    return {
      upload: {
        atags: [],
        files: [],
        inputs: {}
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
    'addUploadedFile': (state, payload) => {
      state.upload.files.push(payload)
    },
    'flushUploadedFiles': (state, payload) => {
      state.upload.files = []
    },
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
