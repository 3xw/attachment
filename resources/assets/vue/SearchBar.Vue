<template>
    <div>
      <label class="sr-only" for="inlineFormInputGroup">Username</label>
      <div class="input-group mb-2">
        <input v-model="needle" @keyup.enter="search" type="text" class="form-control" id="inlineFormInputGroup" placeholder="RECHERCHE">
        <div type="submit" class="input-group-append">
          <div class="input-group-text">
            <i class="material-icons">search</i>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapGetters, mapActions } from 'vuex'

export default
{
  name:'attachment-search-bar',
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
      this.$store.set('attachment/aParams.search',this.needle)
    }
  }
}
</script>
