<template>
  <section>
    <ul v-if="attachments">
      <li v-for="attachment in attachments">{{attachment.name}}</li>
    </ul>
  </section>
</template>

<script>
import { mapState, mapGetters, mapActions } from 'vuex'

export default
{
  name:'attachments',
  computed:
  {
    ...mapGetters('attachment/attachments',{attachments: 'list'})
  }
}
</script>
