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
        filters: '',
        search: '',
        page: 1,
      },
      tParams:{
        uuid: '',
      },
      pagination: {},
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
    'pagination': (state, payload) => {
      state.pagination = payload
    },
    'settings': (state, payload) => {
      state.settings = payload
    }
  },
}
