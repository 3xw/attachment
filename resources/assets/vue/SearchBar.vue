<template>
    <div>
      <label class="sr-only" for="inlineFormInputGroup">Username</label>
      <div class="input-group mb-2">
        <input v-model="needle" @keyup.enter="search" type="text" class="form-control" id="inlineFormInputGroup" placeholder="RECHERCHE">
        <div type="submit" class="input-group-append bg--blue-light">
          <div class="input-group-text bg--blue-light">
            <icon-search></icon-search>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapGetters, mapActions } from 'vuex'

import iconSearch from './icons/search.vue'

export default
{
  name:'attachment-search-bar',
  components: {
      'icon-search': iconSearch
  },
  props: { aid: String },
  data()
  {
    return {
      needle: ''
    }
  },
  methods:
  {
    search()
    {
      this.$store.set(this.aid + '/aParams', Object.assign(this.$store.get(this.aid + '/aParams'),{ search: this.needle, page: 1 }))
    }
  }
}
</script>
