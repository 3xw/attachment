<template lang="html">
  <div id="attachment-files-index" data-intro="Liste des vos médias." data-position="top">

    <div class="row" >



      <!-- list option -->
      <div class="col-12">
        <table v-if="listStyle" class="table table-bordered table-striped table-condensed table-hover" >
          <tr>
            <th></th>
            <th>Name</th>
            <th>Size</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
          <!-- row -->
          <tr v-for="(file, index) in files" :id="index">
            <td>
              <span v-html="$options.filters.icon(file.type+'/'+file.subtype)"></span>
            </td>
            <td>
              <span v-if="file.title">
                <b>{{file.title}}</b><br/>
              </span>
              {{file.name}}
            </td>
            <td>
              {{file.size | bytesToMegaBytes | decimal(2) }} MB
            </td>
            <td>
              {{file.created}}
            </td>
            <td>
              <div class="btn-group">
                <!-- data -->
                <a class="btn btn-xs btn-simple btn-success btn-icon" target="_blank" role="button" v-bind:href="file.download_link" v-if="settings.actions.indexOf('download') != -1">
                  Download
                </a>
                <a class="btn btn-xs btn-simple btn-info btn-icon edit " role="button" v-on:click="dispatch('show-view-file',aid,index)" v-if="settings.actions.indexOf('view') != -1">
                  <i class="material-icons color--white">visibility</i>
                </a>
                <a class="btn btn-xs btn-simple btn-warning btn-icon edit color--white" role="button" v-on:click="dispatch('show-edit-file',aid,index)" v-if="settings.actions.indexOf('edit') != -1">
                  <i class="material-icons color--white">mode_edit</i>
                </a>
                <a class="btn btn-xs btn-simple btn-danger btn-icon remove color--white" role="button" v-on:click="dispatch('delete-file',aid,index)" v-if="settings.actions.indexOf('delete') != -1">
                  <i class="material-icons color--white">delete</i>
                </a>
              </div>
            </td>

          </tr>
        </table>
      </div>

      <!-- thumb option -->
      <div v-if="!listStyle" v-for="(file, index) in files" :id="index"  class="attachment-files__item col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="card mb-4" >

          <!-- thumb -->
          <attachment-thumb :url="settings.url" :file="file"></attachment-thumb>

          <div class="card-body">

            <!-- infos -->
            <p class="card-text small">
              <span v-if="file.title">{{file.title}}</span>
              <span v-else>{{file.name}}</span>
              <br/>
              {{file.size | bytesToMegaBytes | decimal(2) }} MB<br/>
            </p>

            <!-- buttons -->
            <div class="btn-group">

              <a class="btn btn-xs btn-simple btn-success btn-icon" target="_blank" role="button" v-bind:href="file.download_link" v-if="settings.actions.indexOf('download') != -1">
                Download
              </a>
              <a class="btn btn-xs btn-simple btn-info btn-icon edit color--white" role="button" v-on:click="dispatch('show-view-file',aid,index)" v-if="settings.actions.indexOf('view') != -1">
                <i class="material-icons color--white ">visibility</i>
              </a>
              <a class="btn btn-xs btn-simple btn-warning btn-icon edit color--white" role="button" v-on:click="dispatch('show-edit-file',aid,index)" v-if="settings.actions.indexOf('edit') != -1">
                <i class="material-icons color--white">mode_edit</i>
              </a>
              <a class="btn btn-xs btn-simple btn-danger btn-icon remove color--white" role="button" v-on:click="dispatch('delete-file',aid,index)" v-if="settings.actions.indexOf('delete') != -1">
                <i class="material-icons color--white">delete</i>
              </a>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default
{
  name: 'attachment-files-index',
  props: {
    aid:String,
    listStyle: false,
    settings: {
      type: Object,
      required: true
    },
    files: {
      type: Array,
      required: true
    }
  },
  data: function(){
    return {};
  },
  methods: {
    log:function(input){
      console.log(input);
    },
    dispatch:function(evt,aid,data){
      window.aEventHub[this.aid].$emit(evt, data);
    },
  }
}
</script>
