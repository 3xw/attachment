<template>
  <section class="section-attachment--index">
    <div class="section__header d-flex flex-row justify-content-between align-items-center">
      <h3 class="mb-0">{{attachments.length}} Fichiers</h3>
      <div class="section__filter d-flex flex-row">
        <button type="button" @click="mode = 'mosaic'" name="button" class="btn btn-secondary mb-0 color--white" :class="{active: mode == 'mosaic'}"><i class="material-icons">view_quilt</i></button>
        <button type="button" @click="mode = 'thumb'" name="button" class="btn btn-secondary mb-0 color--white" :class="{active: mode == 'thumb'}"><i class="material-icons">view_module</i></button>
        <button type="button" @click="mode = 'thumbInfo'" name="button" class="btn btn-secondary mb-0 color--white" :class="{active: mode == 'thumbInfo'}"><i class="material-icons">view_list</i></button>
      </div>
    </div>
    <div class="utils--spacer-semi"></div>
    <div v-if="aParams.atags" class="f-flex flex-row">
      <p class="small color--grey d-inline-block">Filtre(s): </p>
      <span class="badge badge-secondary" @click="removeAtag(atag)" :key="atag" v-for="atag in aParams.atags.split(',')">{{atag}} <i class="material-icons">close</i></span>
      <div class="utils--spacer-semi"></div>
    </div>
    <div class="section__index">
      <transition name="fade">
        <div v-if="mode == 'mosaic'">
          <div v-packery='{itemSelector: ".packery-item", percentPosition: true}' id="mosaic" class="row packery-row">
            <div v-for="(attachment, i ) in attachments" :key="i" v-packery-item class="packery-item col-lg-4 col-md-6">
              <attachment :index="i" :aid="aid" :mode="mode" :attachment="attachment"></attachment>
            </div>
          </div>
        </div>
        <div v-else-if="mode == 'thumb'">
          <div class="row" v-if="attachments" >
            <div v-for="(attachment, i ) in attachments" :key="i" class="col-sm-6 col-md-4 col-xl-3">
              <attachment :index="i" :aid="aid" :mode="mode" :attachment="attachment"></attachment>
            </div>
          </div>
        </div>
        <div v-else-if="mode == 'thumbInfo'">
          <table class="table w-100">
            <attachment v-for="(attachment, i ) in attachments" :index="i" :aid="aid" :mode="mode" :attachment="attachment"></attachment>
          </table>
        </div>
      </transition>
    </div>
  </section>
</template>

<script>
import { mapState, mapGetters, mapActions } from 'vuex'
import {packeryEvents} from 'vue-packery-plugin'
import Attachment from './Attachment.vue'

export default
{
  name:'attachments',
  props: { aid: String },
  data(){
    return {
      mode: 'mosaic'
    }
  },
  components:
  {
    'attachment': Attachment
  },
  computed:
  {
    aParams()
    {
      return this.$store.get(this.aid + '/aParams')
    },
    attachments()
    {
      return this.$store.get(this.aid + '/attachments/list')
    },
  },
  methods:
  {
    removeAtag(atag)
    {
      var list = this.aParams.atags.split(',');
      console.log(list);
      for(var i = 0 ; i < list.length ; i++) {
        if(list[i] == atag) list.splice(i, 1)
      }
      this.$store.set(this.aid + '/aParams', Object.assign(this.$store.get(this.aid + '/aParams'),{ atags: list.join(','), page: 1 }))
      this.$forceUpdate()
    }
  }
}
</script>
