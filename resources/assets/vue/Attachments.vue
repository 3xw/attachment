<template>
  <section>
    <div class="row">
      <div v-if="attachments" class="col-sm-6 col-md-4 col-xl-3">
        <attachment v-for="(attachment, i ) in attachments" :index="i" :aid="aid" :attachment="attachment"></attachment>
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
