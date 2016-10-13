<!-- filters -->
<script type="text/x-template" id="attachment-filters">
  <div id="attachment-filters">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 v-show="$parent.search || $parent.tag || $parent.sort.term">
          <span v-show="$parent.tag">
            <span class="label label-primary"><a href="#" @click.prevent="clearTags(), find()"><i class="fa fa-times" style="color:white;" aria-hidden="true"></i></a> Filtre: #{{$parent.tag}}</span>
            &nbsp;
          </span>
          <span v-show="$parent.search">
            <span class="label label-primary"><a href="#" @click.prevent="clearSearch(null,true), find()"><i class="fa fa-times" style="color:white;" aria-hidden="true"></i></a> Filtre: {{$parent.search}} ?</span>
            &nbsp;
          </span>
          <span  v-show="$parent.sort.term" class="label label-info"><a href="#"><i class="fa fa-times" style="color:white;" aria-hidden="true"></i></a> Ordre: {{$parent.sort.term}}</span>
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
          <!--
          <div class="form-group">
            <div class="dropdown">
              <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" >
                Afficher les
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu" >
                <li v-for="(index, type) in types"><a href="#">{{type}}</a></li>
              </ul>
            </div>
          </div>
          -->

          <!-- SORT -->
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

          <!-- SEARCH -->
          <button type="button" class="btn btn-fill btn-success" @click.prevent="find()"><i class="fa fa-search" aria-hidden="true"></i> Chercher</button>

        </div>
      </div>
    </div>
  </div>
</script>
