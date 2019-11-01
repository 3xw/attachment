<template lang="html">
  <section>
    <div class="section__files-list">

      <div class="row">
        <div v-for="(attachment, i ) in selectedFiles" :key="i" class="col-sm-2">
          <attachment :index="i" :aid="aid" mode="thumb" :attachment="attachment" :settings="settings"></attachment>
        </div>
      </div>
      <div class="utils--spacer-semi"></div>
    </div>
    <!-- inputs -->
    <attachment-inputs :aid="aid"></attachment-inputs>

  </section>
</template>
<script>
import { client } from '../js/client.js'
import AttachmentInputs from './AttachmentInputs.vue'
import Attachment from './Attachment.vue'
export default {
  name: 'attachment-edit',
  components:
  {
    'attachment': Attachment,
    'attachment-inputs': AttachmentInputs,
  },
  props: { aid: String, settings: Object },
  data() {
    return {
      attachmentInputs: {
        title: null,
        date: null,
        description: null,
        author: null,
        copyright: null,
        atags: [],
      }
    }
  },
  mounted()
  {
    this.mergeDatas()
  },
  computed:
  {
    aParams()
    {
      return this.$store.get(this.aid + '/aParams')
    },
    selectedFiles()
    {
      return this.$store.get(this.aid + '/selection.files')
    }
  },
  watch:
  {
    selectedFiles: { handler(){ this.mergeDatas() }, deep: true },
  },
  methods:
  {
    mergeDatas()
    {
      console.log('Yo');
      this.atags = []
      for(let i = 0;i < this.selectedFiles.length; i++){
        let attachment = this.selectedFiles[i]
        this.attachmentInputs.title
        //Object.assign(this.attachmentInputs, this.selectedFiles[i]);
      }
      this.$store.set(this.aid + '/aParams', Object.assign(this.$store.get(this.aid + '/aParams'),{ atags: this.attachmentInputs.atags.join(','), page: 1 }))

      console.log(this.attachmentInputs);
    },
    edit()
    {
      //SAVE this.selectedFiles
    }
  }
}
</script>
