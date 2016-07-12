<!-- style -->
<?php include_once('includes/add-edit__style.html'); ?>

<!-- includes settings -->
<?php include_once('includes/add-edit__settings.php'); ?>

<!-- pass settings -->
<div id="attachment-settings" data-settings='<?= json_encode($attachemntSettings) ?>' ></div>

<!-- dropzone -->
<?php include_once('includes/add-edit__dropzone.html'); ?>

<!-- files -->
<?php include_once('includes/add-edit__files.html'); ?>

<!-- pagination -->
<script type="text/x-template" id="attachment-pagination">
  <div id="attachment-pagination">
    <nav>
      <ul class="pagination">
        <li v-bind:class="{ 'disabled': !pagination.has_prev_page}">
          <a href="#" aria-label="Previous" @click.prevent="changePage(pagination.current_page - 1)">
            <span aria-hidden="true">&laquo;</span>
          </a>
        </li>
        <li v-for="( index, num ) in array" :class="{'active': num == pagination.current_page}">
          <a href="#" @click.prevent="changePage(num)">{{ num }}</a>
        </li>
        <li v-bind:class="{ 'disabled': !pagination.has_next_page}">
          <a href="#" aria-label="Next" @click.prevent="changePage(pagination.current_page + 1)">
            <span aria-hidden="true">&raquo;</span>
          </a>
        </li>
      </ul>
    </nav>
    Page {{pagination.current_page}} sur {{pagination.page_count}} ( total: {{pagination.count}}  médias )
  </div>
</script>

<!-- filters -->
<script type="text/x-template" id="attachment-filters">
  <div id="attachment-filters">
    <div class="panel panel-default">
      <div class="panel-heading">
        Filtrer les résultats
      </div>
      <div class="panel-body">
        <div class="form-inline">
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon" id="basic-addon1">#</span>
              <input type="text" class="form-control" id="exampleInputName2" placeholder="tag">
            </div>
          </div>
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon" id="basic-addon1">?</span>
              <input type="text" class="form-control" id="exampleInputName2" placeholder="nom du fichier ou titre">
            </div>
          </div>
          <button type="button" class="btn btn-default">Chercher</button>
          <div class="form-group">
            <div class="dropdown">
              <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                Ordonner par
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="#">Separated link</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</script>

<!-- browse -->
<script type="text/x-template" id="attachment-browse">
  <div id="attachment-browse" class="modal-mask" v-show="show" transition="modal">
    <div class="modal-wrapper">
      <div class="modal-container container">
        <div class="custom-modal-header">
          <?= __('Parcourir les fichiers') ?>
        </div>
        <div class="custom-modal-body">

        <!-- pagination -->
        <attachment-filters :pagination.sync="pagination" :callback="getFiles" ></attachment-filters>

        <!-- file list -->
        <div class="row" >
          <div v-for="(index, file) in files" id="{{index}}"  class="attachment-files__item col-xs-4 col-md-2">
            <div class="thumbnail" >
              <img v-bind:src="'<?= $this->Url->build('/image.php') ?>?image='+file.path+'&width=678&cropratio=16:9'" />
              <div class="caption">
                {{file.name}}<br/>
                {{file.size | bytesToMegaBytes | decimal 2 }} MB<br/>

                  <!-- data -->
                  <a v-show="!isSelected(file.id)" href="#" class="btn btn-xs btn-info" role="button" @click="add(index)" >Ajouter</a>
                  <a v-show="isSelected(file.id)" href="#" class="btn btn-xs btn-warning" role="button" @click="remove(file.id)" >Enlever</a>

              </div>
            </div>
          </div>
        </div>

        <!-- pagination -->
        <attachment-pagination :pagination.sync="pagination" :callback="getFiles" :offset="4"></attachment-pagination>

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

<div id="attachment-app">
  <attachment-upload :show.sync="showUpload" :settings="settings" ></attachment-upload>
  <attachment-browse :selectedfiles.sync="selectedfiles" :show.sync="showBrowse" :settings="settings" ></attachment-browse>
  <attachment-files :files.sync="selectedfiles" ></attachment-files>
  <p>
    <div class="btn-group">
      <button type="button" class="btn btn-xs btn-info" @click="$children[0].open()">
        <i class="fa fa-cloud-upload" aria-hidden="true"></i>
        <?= __('Téléverser'); ?>
      </button>
      <button type="button" class="btn btn-xs btn-info" @click="$children[1].open()">
        <i class="fa fa-cloud" aria-hidden="true"></i>
        <?= __('Parcourir'); ?>
      </button>
    </div>
  </p>
</div>
