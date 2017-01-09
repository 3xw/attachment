<!-- input -->
<script type="text/x-template" id="attachment-index">
  <div class="attachment-index">

    <!-- filters -->
    <attachment-filters :types="types" :tags="tags" :callback="getFiles" ></attachment-filters>

    <!-- WARNINGS -->
    <div v-for="(index, error) in errors" track-by="$index" class="alert alert-warning alert-dismissible" role="alert">
      <button type="button" class="close"  aria-label="Close" @click="errors = []" ><span aria-hidden="true">&times;</span></button>
      <strong>Attention!</strong> {{error}}
    </div>

    <!-- SUCCESS -->
    <div v-for="(index, success) in successes" track-by="$index" class="alert alert-success alert-dismissible" role="alert">
      <button type="button" class="close"  aria-label="Close" @click="successes = []" ><span aria-hidden="true">&times;</span></button>
      <strong>OK!</strong> {{success}}
    </div>

    <!-- loading -->
    <div v-if="loading" class="attachment-loading-container">
      <img v-bind:src="this.settings.url+'attachment/img/loading.gif'" class="img-responsive" />
    </div>

    <!-- files index -->
    <attachment-files-index :settings.sync="settings" :files.sync="files"></attachment-files-index>

    <!-- pagination -->
    <attachment-pagination :pagination="pagination" :callback="getFiles" :offset="4"></attachment-pagination>

    <!-- edit -->
    <attachment-edit :settings.sync="settings" ></attachment-edit>

  </div>
</script>
