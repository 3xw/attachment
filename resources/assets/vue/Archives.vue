<template lang="html">
  <div class="block-attachment--archive">
    <div class="block-attachment--archive-box" :class="{active: open}"> <!--v-if="archives"-->
      <div class="block-attachment__header">
        Téléchargements <i class="material-icons pointer" @click="open = false">clear</i>
      </div>
      <div class="block-attachment__content">
        <!--<ul class="list-unstyled">
          <li><span><i class="material-icons">folder_open</i>&nbsp; archive-dg3h4f.zip</span> <img src="https://static.wgr.ch/attachment/loading.gif" alt=""></li>
          <li><span><i class="material-icons">folder_open</i>&nbsp; archive-wf842e.zip</span> <i class="material-icons"> cloud_download </i></li>
        </ul>-->
        <ul v-for="archive in archives">
          <span><i class="material-icons">folder_open</i>&nbsp; {{archive.name}}</span> {{archive.status}}</li>
        </ul>
        <div v-if="selectedFiles" @click="requestArchive">
          REQUEST ARCHIVE
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { client } from '../js/client.js'
export default {
  name: 'archive-request',
  props: {
    aid: String,
    settings: Object,
  },
  data()
  {
    return {
      open: true
    }
  },
  computed:
  {
    selectedFiles()
    {
      return this.$store.get(this.aid + '/selection.files')
    },
    archives()
    {
      return this.$store.get(this.aid + '/aarchives')
    }
  },
  created(){
  },
  watch:
  {
    selectedFiles: function(){
    },
    aarchives: function(){
    }
  },
  methods: {
    requestArchive()
    {
      let params = {
        headers: {'Accept': 'application/json', 'Content-Type': "application/x-www-form-urlencoded"},
      }
      let formData = new FormData()
      for(let i = 0;i < this.selectedFiles.length;i++) formData.append('aids['+i+']', this.selectedFiles[i].id)
      client.post(this.settings.url+'attachment/aarchives/add', formData, params)
      .then(this.archiveSuccess, this.archiveError)
    },
    archiveSuccess(response){

    },
    archiveError(response){

    }
  }
}
</script>
