<template lang="html">
  <div class="attachment-thumb__icon-container" >
    <div>
      <img v-if="$options.filters.isThumbable(file)" v-bind:src="url+'thumbnails/'+file.profile+'/w678c16-9/'+file.path" class="card-img-top" />
      <span v-html="$options.filters.icon(file.type+'/'+file.subtype)"></span>
    </div>
  </div>
</template>

<script>
export default
{
  name: 'attachment-thumb',
  props: {
    file: {},
    url: ''
  }
}
</script>
