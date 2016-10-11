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

        <!-- file list -->
        <div class="row" >
          <div v-for="(index, file) in files" id="{{index}}"  class="attachment-files__item col-xs-4 col-md-2">
            <div class="thumbnail" >

              <!-- thumb -->
              <attachment-thumb :url="settings.url" :file="file"></attachment-thumb>

              <div class="caption">
                {{file.name | truncate 15 }}<br/>
                {{file.size | bytesToMegaBytes | decimal 2 }} MB<br/>

                  <!-- data -->
                  <a v-show="!isSelected(file.id)" href="#" class="btn btn-xs btn-info" role="button" @click="add(index)" >Ajouter</a>
                  <a v-show="isSelected(file.id)" href="#" class="btn btn-xs btn-warning" role="button" @click="remove(file.id)" >Enlever</a>

              </div>
            </div>
          </div>
        </div>

        <!-- pagination -->
        <attachment-pagination :pagination="pagination" :callback="getFiles" :offset="4"></attachment-pagination>

        <p></p>
        <div class="custom-modal-footer">
          <div class="btn-group">
            <button type="button" class="modal-default-button btn btn-warning" @click="close()">
              Fermer
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</script>
