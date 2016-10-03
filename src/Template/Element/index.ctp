<?php
// add css
$this->Html->css([
  'Attachment.vendor/TimSchlechter/bootstrap-tagsinput/bootstrap-tagsinput.css',
  'Attachment.attachment.css',
],['block' => 'css']);

// add js scripts
$this->Html->script([
  'Attachment.vendor/TimSchlechter/bootstrap-tagsinput/bootstrap-tagsinput.js',
  'Attachment.vendor/twitter/typeahead.js/typeahead.bundle.min.js',
  'Attachment.vendor/rubaxa/Sortable/Sortable.js',
  'Attachment.index.js'
],['block' => 'script']);
?>
<!-- includes settings -->
<?php include_once('includes/add-edit__settings.php'); ?>

<!-- pass settings -->
<div id="attachment-settings" data-settings='<?= json_encode($attachemntSettings) ?>' ></div>

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
        <h4 v-show="$parent.search || $parent.tag || sort.term">
          <span v-show="$parent.tag">
            <span class="label label-primary"><a href="#" @click.prevent="clearTags(), find()"><i class="fa fa-times" style="color:white;" aria-hidden="true"></i></a> Filtre: #{{$parent.tag}}</span>
            &nbsp;
          </span>
          <span v-show="$parent.search">
            <span class="label label-primary"><a href="#" @click.prevent="clearSearch(null,true), find()"><i class="fa fa-times" style="color:white;" aria-hidden="true"></i></a> Filtre: {{$parent.search}} ?</span>
            &nbsp;
          </span>
          <span  v-show="sort.term" class="label label-info"><a href="#"><i class="fa fa-times" style="color:white;" aria-hidden="true"></i></a> Ordre: {{sort.term}}</span>
        </h4>
      </div>
      <div class="panel-body">
        <div class="form-inline">
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon" id="basic-addon1">#</span>
              <!-- ici -->
              <select class="form-control" id="tagsInputSearch" placeholder="tag" ></select>
            </div>
          </div>
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon" id="basic-addon1">?</span>
              <input type="text" class="form-control" id="searchInputSearch" placeholder="nom du fichier ou titre">
            </div>
          </div>

          <!-- TYPES -->
          <div class="form-group">
            <div class="dropdown">
              <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" >
                Afficher les
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu" >
                <li v-for="(index, type) in types"><a href="#">{{type.type}}</a></li>
              </ul>
            </div>
          </div>

          <!-- SORT -->
          <div class="form-group">
            <div class="dropdown">
              <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" >
                Ordonner par
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu" >
                <li><a href="#">nom</a></li>
                <li><a href="#">date de création</a></li>
                <li><a href="#">type</a></li>
              </ul>
            </div>
          </div>

          <!-- SEARCH -->
          <button type="button" class="btn btn-success" @click.prevent="find()"><i class="fa fa-search" aria-hidden="true"></i> Chercher</button>

        </div>
      </div>
    </div>
  </div>
</script>

<!-- browse -->
<script type="text/x-template" id="attachment-browse">
  <div id="attachment-browse" v-if="show">
    <!-- title -->
    <h4><?= __('Parcourir les fichiers') ?></h4>

    <!-- pagination -->
    <attachment-filters :types="types" :tags="tags" :callback="getFiles" ></attachment-filters>

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
  </div>
</script>

<div id="attachment-index-app">
  <attachment-browse :types="types" :tags="tags" :selectedfiles.sync="selectedfiles" :show.sync="showBrowse" :settings="settings" ></attachment-browse>
</div>
