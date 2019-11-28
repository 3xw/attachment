export default
{
  namespaced: true,
  state()
  {
    return {
      selection: {
        files: [],
        token: '',
      },
      upload: {
        atags: [],
        files: [],
        inputs: {}
      },
      aParams:{
        uuid: '',
        atags: '',
        filters: '',
        type: 'image/*',
        sort: 'created_desc',
        search: '',
        refresh: '',
        page: 1,
      },
      tParams:{
        uuid: '',
      },
      pagination: {},
      settings: {},
      preview: {},
      aarchives: []
    }
  },
  mutations:
  {
    // selection
    'addFileToSelection': (state, payload) => {
      state.selection.files.push(payload)
    },
    'removeFileFromSelection': (state, payload) => {
      let idx = state.selection.files.findIndex(file => file === payload);
      state.selection.files.splice(idx, 1)
    },
    'flushSelection': (state, payload) => {
      state.selection.files = []
    },
    'selection.files': (state, payload) => {
      state.selection.files = payload
    },
    'selection.token': (state, payload) => {
      state.selection.token = payload
    },
    // upload
    'addUploadedFile': (state, payload) => {
      state.upload.files.push(payload)
    },
    'flushUploadedFiles': (state, payload) => {
      state.upload.files = []
    },
    'upload': (state, payload) => {
      state.upload = payload
    },
    // browse
    'aParams': (state, payload) => {
      state.aParams = payload
    },
    'pagination': (state, payload) => {
      state.pagination = payload
    },
    // settings
    'settings': (state, payload) => {
      state.settings = payload
    },
    //Preview
    'preview': (state, payload) => {
      state.preview = payload
    },
    //Preview
    'aarchives': (state, payload) => {
      state.aarchives = payload
    },
    'addAarchives': (state, payload) => {
      state.aarchives.push(payload)
    },
    'flushAarchives': (state, payload) => {
      state.aarchives = []
    }
  },
}
