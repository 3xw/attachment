<!-- browse -->
<script type="text/x-template" id="attachment-browse">
  <div id="attachment-browse" class="modal-mask" v-if="show" transition="modal">
    <div class="modal-wrapper">
      <div class="modal-container container">
        <div class="custom-modal-header">
          <?= __('Parcourir les fichiers') ?>
        </div>
        <div class="custom-modal-body">

        <!-- filters -->
        <attachment-filters :types="types" :tags="tags" :callback="getFiles" ></attachment-filters>

        <!-- WARNINGS -->
        <div v-for="(index, error) in errors" track-by="$index" class="alert alert-warning alert-dismissible" role="alert">
          <button type="button" class="close"  aria-label="Close" @click="errors = []" ><span aria-hidden="true">&times;</span></button>
          <strong>Attention!</strong> {{error}}
        </div>

        <!-- loading -->
        <div v-if="loading" class="attachment-loading-container">
          <img v-bind:src="this.settings.url+'attachment/img/loading.gif'" class="img-responsive" />
        </div>

        <!-- file list -->
        <div v-if="!loading" class="row" >
          <div v-for="(index, file) in files" id="{{index}}"  class="attachment-files__item col-xs-4 col-md-2">
            <div class="thumbnail" >

              <!-- thumb -->
              <attachment-thumb :url="settings.url" :file="file"></attachment-thumb>

              <div class="caption">
                {{file.name | truncate 15 }}<br/>
                {{file.size | bytesToMegaBytes | decimal 2 }} MB<br/>

                  <!-- data -->
                  <a v-show="!isSelected(file.id)" href="#" class="btn btn-xs btn-fill btn-info" role="button" @click="add(index)" >Ajouter</a>
                  <a v-show="isSelected(file.id)" href="#" class="btn btn-xs btn-fill btn-warning" role="button" @click="remove(file.id)" >Enlever</a>

              </div>
            </div>
          </div>
        </div>

        <!-- pagination -->
        <attachment-pagination :pagination="pagination" :callback="getFiles" :offset="4"></attachment-pagination>

        <p></p>
        <div class="custom-modal-footer">
          <div class="btn-group">
            <button type="button" class="modal-default-button btn btn-fill btn-warning" @click="close()">
              Fermer
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</script>
