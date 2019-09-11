<template>
  <section class="">

    <!-- progress -->
    <div class="progress">
      <div class="progress-bar" role="progressbar" :style="{ width: percent + '%' }" >{{percent}}</div>
    </div>


  </section>
</template>

<script>
export default
{
  name:'attachment-upload',
  props: { aid: String },
  data()
  {
    return {
      progress: 0,
      uploading: false
    }
  },
  computed:
  {
    percent()
    {
      return Math.floor(this.progress * 100)
    }
  }
}
</script>
