<template>
  <section class="section-attachment--index">
    <div class="section__header d-flex flex-row justify-content-between align-items-center">
      <h3 class="mb-0">{{attachments.length}} Fichiers</h3>
      <div class="section__filter d-flex flex-row">
        <button type="button" @click="mode = 'thumb'" name="button" class="btn btn-secondary mb-0 color--white" :class="{active: mode == 'thumb'}"><i class="material-icons">view_module</i></button>
        <button type="button" @click="mode = 'thumbInfo'" name="button" class="btn btn-secondary mb-0 color--white" :class="{active: mode == 'thumbInfo'}"><i class="material-icons">view_list</i></button>
      </div>
    </div>
    <div class="utils--spacer-semi"></div>
    <div class="section__index">
      <div class="row">
        <div v-if="attachments" class="col-sm-6 col-md-4 col-xl-3">
          <attachment v-for="(attachment, i ) in attachments" :index="i" :aid="aid" :attachment="attachment"></attachment>
        </div>
      </div>
    </div>

  </section>
</template>

<script>
import { mapState, mapGetters, mapActions } from 'vuex'

import Attachment from './Attachment.vue'

export default
{
  name:'attachments',
  props: { aid: String },
  data(){
    return {
      mode: 'thumb'
    }
  },
  components:
  {
    'attachment': Attachment
  },
  computed:
  {
    attachments()
    {
      return this.$store.get(this.aid + '/attachments/list')
    },
  }
}
</script>
