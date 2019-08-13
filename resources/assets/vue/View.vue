<template lang="html">
  <div id="attachment-view" class="modal-mask" v-if="show" transition="modal">
    <div class="modal-wrapper">
      <div class="modal-container container" style="max-height: 600px; scroll:auto;">
        <div class="custom-modal-header">
          <h4 v-html="$options.filters.icon(file.type+'/'+file.subtype)"></h4>
        </div>
        <div class="custom-modal-body" >

          <div>

            <!-- url -->
            <p v-if="$options.filters.isNotEmbed(file)">
              Url: {{settings.baseUrls[file.profile]}}{{file.path}}
            </p>

            <!-- url -->
            <p>
              Type: {{file.type+'/'+file.subtype}}
            </p>

            <!-- embed -->
            <div v-if="$options.filters.isEmbed(file)" v-html="file.embed"></div>

            <!-- thumb -->
            <img v-if="$options.filters.isNiceImage(file)" v-bind:src="settings.url+'thumbnails/'+file.profile+'/w1200/'+file.path" class="img-fluid" />
          </div>

        </div>
        <p></p>
        <div class="custom-modal-footer">
          <div class="btn-group">
            <button type="button" class="modal-default-button btn btn-warning" @click="close()">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default
{
  name: 'attachment-view',
  data: function(){
    return {
      file: {},
      show: false
    };
  },
  props: {
    aid:String,
    settings: Object,
  },
  created: function(){
    var instance = this;
    window.aEventHub[this.aid].$on('view-file',function(file) {
      //console.log(file);
      instance.file = file;
      instance.open();
    });
  },
  methods: {
    close: function(){
      this.show = false;
    },
    open: function(){
      this.show = true;
      setTimeout(this.setupUI, 500);
    },
    setupUI: function(){
      //console.log('setup UI');
    },
  }
}
</script>
