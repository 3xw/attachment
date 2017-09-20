<!-- filters -->
<script type="text/x-template" id="attachment-filters">
  <div id="attachment-filters">
    <div class="card border-0">
      <div class="card-header border-0 bg-white">
        <h4 v-show="$parent.search || $parent.tag || $parent.sort.term">
          <span v-show="$parent.tag">
            <span class="badge badge-primary"><a href="#" @click.prevent="clearTags(), find()"><i class="fa fa-times" style="color:white;" aria-hidden="true"></i></a> <?= __d('Attachment','Filter') ?>: #{{$parent.tag}}</span>
            &nbsp;
          </span>
          <span v-show="$parent.search">
            <span class="badge badge-primary"><a href="#" @click.prevent="clearSearch(null,true), find()"><i class="fa fa-times" style="color:white;" aria-hidden="true"></i></a> <?= __d('Attachment','Filter') ?>: {{$parent.search}} ?</span>
            &nbsp;
          </span>
          <span v-show="$parent.sort.term" class="badge badge-info"><a href="#"><i class="fa fa-times" style="color:white;" aria-hidden="true"></i></a> <?= __d('Attachment','Order') ?>: {{$parent.sort.term}}</span>
        </h4>
      </div>
      <div class="card-body">
        <div class="form-inline">

          <!-- tags -->
          <!--
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon" id="basic-addon1">#</span>
              <select class="form-control" id="tagsInputSearch" placeholder="<?= __d('Attachment','tag') ?>" ></select>
            </div>
          </div>
          -->

          <!-- serach -->
          <div class="form-group">
            <div class="input-group">
              <span class="input-group-addon" id="basic-addon1">?</span>
              <input type="text" class="form-control" id="searchInputSearch" placeholder="<?= __d('Attachment','file name or title') ?>">
            </div>
          </div>

          <!-- TYPES -->
          <!--
          <div class="form-group">
            <div class="dropdown">
              <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" >
                Afficher les
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu" >
                <li v-for="(type, index) in types"><a href="#">{{type}}</a></li>
              </ul>
            </div>
          </div>
          -->

          <!-- SORT -->
          <!--
          <div class="form-group">
            <div class="dropdown">
              <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" >
                Ordonner par
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu" >
                <li><a @click="sortHandler('name','nom')">nom</a></li>
                <li><a @click="sortHandler('created','date de création')">date de création</a></li>
                <li><a @click="sortHandler('subtype','type')">type</a></li>
              </ul>
            </div>
          </div>
          -->

          <!-- SEARCH -->
          <button type="button" class="btn btn-fill btn-success" @click.prevent="find()"><i class="fa fa-search" aria-hidden="true"></i> <?= __d('Attachment','Search') ?></button>

        </div>
      </div>
    </div>
  </div>
</script>
