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

    <!-- view -->
    <attachment-view :settings.sync="settings" ></attachment-view>

    <!-- edit -->
    <attachment-edit :settings.sync="settings" ></attachment-edit>

    <!-- upload -->
    <attachment-upload :settings.sync="settings" ></attachment-upload>

    <!-- embed -->
    <attachment-embed :settings.sync="settings" ></attachment-embed>

    <!-- add btn -->
    <p>
      <div v-if="settings.actions.indexOf('add') != -1" class="btn-group" data-intro="Ajouter des médias à l'aide de ces boutons" data-position="right">
        <button type="button" class="btn btn-fill btn-xs btn-info" @click="$broadcast('show-upload')">
          <i class="fa fa-cloud-upload" aria-hidden="true"></i>
          <?= __('Téléverser'); ?>
        </button>
        <button v-if="dispalyEmbed()" type="button" class="btn btn-fill btn-xs btn-info" @click="$broadcast('show-embed')">
          <i class="fa fa-code" aria-hidden="true"></i>
          <?= __('Ajouter un embed code'); ?>
        </button>
      </div>
    </p>

  </div>
</script>
