<template>
  <section>
    <div v-if="attachments">
      <!--<attachment v-for="attachment in attachments" :attachment="attachment"></attachment>-->
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
