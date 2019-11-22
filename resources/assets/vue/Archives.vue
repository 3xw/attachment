<template lang="html">
  <div class="block-attachment--archive">
    <div class="block-attachment--archive-box" :class="{active: open}"> <!--v-if="archives"-->
      <div class="block-attachment__header">
        Téléchargements <i class="material-icons pointer" @click="open = false">clear</i>
      </div>
      <div class="block-attachment__content">
        <ul class="list-unstyled">
          <li><span><i class="material-icons">folder_open</i>&nbsp; archive-dg3h4f.zip</span> <img src="https://static.wgr.ch/attachment/loading.gif" alt=""></li>
          <li><span><i class="material-icons">folder_open</i>&nbsp; archive-wf842e.zip</span> <i class="material-icons"> cloud_download </i></li>
        </ul>
        <!--<ul>
          <li v-for="archive in archives">{{archive.attachment.name}} {{archive.status}}</li>
        </ul>-->
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
      open: false
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
      return this.$store.get(this.aid + '/archives')
    }
  },
  created(){
    console.log('ARCHIVE REQUEST CREATED');
    console.log(this.selectedFiles);
  },
  watch:
  {
    selectedFiles: function(){
      console.log('SELECTED FILES CHANGES');
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
      console.log('SUCCESS')
      console.log(response)
    },
    archiveError(response){
      console.log('ERROR')
      console.log(response)
    }
  }
}
</script>
