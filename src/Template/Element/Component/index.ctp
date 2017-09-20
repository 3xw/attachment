<!-- input -->
<script type="text/x-template" id="attachment-index">
  <div class="attachment-index">

    <!-- filters -->
    <attachment-filters :types="types" :tags="tags" :callback="getFiles" ></attachment-filters>

    <!-- WARNINGS -->
    <div v-for="(error, index) in errors" track-by="$index" class="alert alert-warning alert-dismissible" role="alert">
      <button type="button" class="close"  aria-label="Close" @click="errors = []" ><span aria-hidden="true">&times;</span></button>
      <strong><?= __d('Attachment','Watch out') ?>!</strong> {{error}}
    </div>

    <!-- SUCCESS -->
    <div v-for="(success, index) in successes" track-by="$index" class="alert alert-success alert-dismissible" role="alert">
      <button type="button" class="close"  aria-label="Close" @click="successes = []" ><span aria-hidden="true">&times;</span></button>
      <strong><?= __d('Attachment','Ok') ?>!</strong> {{success}}
    </div>

    <!-- loading -->
    <div v-if="loading" class="attachment-loading-container">
      <img v-bind:src="this.settings.url+'attachment/img/loading.gif'" class="img-responsive" />
    </div>

    <!-- files index -->
    <attachment-files-index :settings="settings" :files="files"></attachment-files-index>

    <!-- pagination -->
    <attachment-pagination :pagination="pagination" :callback="getFiles" :settings="settings"></attachment-pagination>

    <!-- view -->
    <attachment-view :settings="settings" ></attachment-view>

    <!-- edit -->
    <attachment-edit :settings="settings" ></attachment-edit>

    <!-- upload -->
    <attachment-upload :settings="settings" ></attachment-upload>

    <!-- embed -->
    <attachment-embed :settings="settings" ></attachment-embed>

    <!-- add btn -->
    <p>
      <div v-if="settings.actions.indexOf('add') != -1" class="btn-group" data-intro="Ajouter des médias à l'aide de ces boutons" data-position="right">
        <button type="button" class="btn btn-fill btn-xs btn-info" @click="dispatch('show-upload')">
          <i class="fa fa-cloud-upload" aria-hidden="true"></i>
          <?= __d('Attachment','Upload') ?>
        </button>
        <button v-if="dispalyEmbed()" type="button" class="btn btn-fill btn-xs btn-info" @click="dispatch('show-embed')">
          <i class="fa fa-code" aria-hidden="true"></i>
        <?= __d('Attachment','Add an embed code') ?>
        </button>
      </div>
    </p>

  </div>
</script>
