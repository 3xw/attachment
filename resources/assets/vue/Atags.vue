<template lang="html">
  <section class="section-attachment--atags">
    <ul class="list-unstyled section-attachment__list" v-if="atagTypes">
      <li v-if="!upload">
        <div class="section-attachment__list-title d-flex flex-row justify-content-between" :class="{active: types.isActive}" @click="types.isActive = !types.isActive;$forceUpdate()">
          <p class="text--upper mb-0">{{types.name}}</p> <i class="material-icons">{{(types.isActive)? 'keyboard_arrow_up' : 'keyboard_arrow_down'}}</i>
        </div>
        <ul class="list-unstyled section-attachment__sublist" v-if="types.isActive">
          <li v-for="(option, i2) in types.options" :key="i2" @click="types.current = option.slug;$forceUpdate();filterType(option.slug);" class="d-flex flex-row justify-content-between align-items-center" :class="{active: types.current == option.slug}">
            {{option.name}} <input type="radio" :checked="types.current == option.slug">
          </li>
        </ul>
      </li>
      <li v-if="types.current != 'application' && !upload" v-for="(filter, i) in filters">
        <div class="section-attachment__list-title d-flex flex-row justify-content-between" :class="{active: filter.isActive}" @click="filter.isActive = !filter.isActive;$forceUpdate()">
          <p class="text--upper mb-0">{{filter.name}}</p> <i class="material-icons">{{(filter.isActive)? 'keyboard_arrow_up' : 'keyboard_arrow_down'}}</i>
        </div>
        <ul class="list-unstyled section-attachment__sublist" v-if="filter.isActive">
          <li v-for="(option, i2) in filter.options" :key="i2" @click="option.isActive = !option.isActive;$forceUpdate();filterOption(option.slug); " class="d-flex flex-row justify-content-between align-items-center" :class="{active: checkFilterActive(i, i2, option.slug)}">
            {{option.name}} <input type="checkbox" :checked="checkFilterActive(i, i2, option.slug)">
          </li>
        </ul>
      </li>
      <li v-for="(atagType, index1) in atagTypes">
        <div class="section-attachment__list-title d-flex flex-row justify-content-between" :class="{active: atagType.isActive}" @click="atagType.isActive = !atagType.isActive;$forceUpdate()">
          <p class="text--upper mb-0">{{atagType.name}}</p> <i class="material-icons">{{(atagType.isActive)? 'keyboard_arrow_up' : 'keyboard_arrow_down'}}</i>
        </div>
        <ul class="list-unstyled section-attachment__sublist" v-if="atagType.isActive">
          <li v-for="(atag, index2) in atagType.atags" :key="atag.id" @click="toggle(index1, index2)" class="d-flex flex-row justify-content-between align-items-center" :class="{active: ((upload)? atag.isActive : checkActive(index1, index2, atag.name))}">
            {{atag.name}} <input type="checkbox" :checked="(upload)? atag.isActive : checkActive(index1, index2, atag.name)">
          </li>
        </ul>
      </li>
    </ul>
  </section>
</template>
<script>
import { mapState, mapGetters, mapActions } from 'vuex'

export default
{
  name:'attachment-atags',
  props: { aid: String, upload:Boolean },
  data(){
    return {
      types: {
        name: 'Types',
        slug: 'type',
        isActive: false,
        current: 'image',
        options: [
          {
            name: 'Images',
            slug: 'image',
          },
          {
            name: 'Vidéos',
            slug: 'video',
          },
          {
            name: 'Autres',
            slug: 'application',
          }
        ]
      },
      filters: [
        {
          slug: 'orientation',
          name: 'Orientation',
          isActive: false,
          options: [
            {
              slug: 'vertical',
              name: 'Vertical',
              isActive: false
            },
            {
              slug: 'horizontal',
              name: 'Horizontal',
              isActive: false
            },
            {
              slug: 'square',
              name: 'Carré',
              isActive: false
            }
          ]
        }
      ]
    }
  },
  computed:
  {
    atagTypes()
    {
      return this.$store.get(this.aid + '/atags/list').map((v, idx) => {
        let atags = v.atags.map((v2, idx2) => Object.assign({index: idx2}, v2))
        return Object.assign({index: idx}, v, {atags: atags, isActive: false})
      })
    },
    aParams()
    {
      return this.$store.get(this.aid + '/aParams')
    },
  },
  created()
  {
    //this.$store.set(this.aid + '/aParams', Object.assign(this.$store.get(this.aid + '/aParams'),{ filters: filters.join(','), page: 1 }))
  },
  methods:
  {
    toggle(index1,index2)
    {
      // test exclusive
      if(this.atagTypes[index1].exclusive) for(let i in this.atagTypes[index1].atags) if(i != index2) this.atagTypes[index1].atags[i].isActive = false

      // toogle
      if(!this.atagTypes[index1].atags[index2].isActive) this.atagTypes[index1].atags[index2].isActive = true
      else this.atagTypes[index1].atags[index2].isActive = false

      // force render
      this.$forceUpdate()

      // loop
      let atags = []
      for(let i1 in this.atagTypes)for(let i2 in this.atagTypes[i1].atags) if(this.atagTypes[i1].atags[i2].isActive) atags.push(this.atagTypes[i1].atags[i2].name)

      // set upload tags OR fetch attachment by mutating aParams
      if(this.upload) this.$store.set(this.aid + '/upload', Object.assign(this.$store.get(this.aid + '/upload'),{ atags: atags }))
      else this.$store.set(this.aid + '/aParams', Object.assign(this.$store.get(this.aid + '/aParams'),{ atags: atags.join(','), page: 1 }))
    },
    filterType()
    {
      if(!this.upload){
        this.$store.set(this.aid + '/aParams', Object.assign(this.$store.get(this.aid + '/aParams'),{ type: this.types.current, filters: '', atags: '', page: 1 }))
      }
    },
    filterOption(key)
    {
      if(!this.upload){
        let filters = []
        for(let i1 in this.filters)for(let i2 in this.filters[i1].options) if(this.filters[i1].options[i2].isActive) filters.push(this.filters[i1].options[i2].slug)
        this.$store.set(this.aid + '/aParams', Object.assign(this.$store.get(this.aid + '/aParams'),{ filters: filters.join(','), page: 1 }))
      }
    },
    checkActive(index1, index2, atag){
      this.atagTypes[index1].atags[index2].isActive = (this.aParams.atags.indexOf(atag) !== -1)
      return this.aParams.atags.indexOf(atag) !== -1
    },
    checkFilterActive(index1, index2, filter){
      this.filters[index1].options[index2].isActive = (this.aParams.filters.indexOf(filter) !== -1)
      return this.aParams.filters.indexOf(filter) !== -1
    }
  }
}
</script>
