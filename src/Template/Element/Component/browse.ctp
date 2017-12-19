<!-- browse -->
<script type="text/x-template" id="attachment-browse">
  <div id="attachment-browse" class="modal-mask" v-if="show" transition="modal">
    <div class="modal-wrapper">
      <div class="modal-container container">
        <div class="custom-modal-header">
          <?= __d('Attachment','Browse files') ?>
        </div>
        <div class="custom-modal-body">

          <!-- filters -->
          <attachment-filters :types="types" :tags="tags" :callback="getFiles" ></attachment-filters>

          <!-- WARNINGS -->
          <div v-for="(error, index) in errors" track-by="$index" class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close"  aria-label="Close" @click="errors = []" ><span aria-hidden="true">&times;</span></button>
            <strong><?= __d('Attachment','Watch out') ?>!</strong> {{error}}
          </div>

          <!-- loading -->
          <div v-if="loading" class="attachment-loading-container">
            <img v-bind:src="this.settings.url+'attachment/img/loading.gif'" class="img-responsive" />
          </div>

          <!-- file list -->
          <div v-if="!loading" class="row">

            <!-- list option -->
            <div class="col-12">
              <table v-if="listStyle" class="table table-bordered table-striped table-condensed table-hover">
                <!-- row -->
                <tr>
                  <th></th>
                  <th><?= __('Name') ?></th>
                  <th><?= __('Size') ?></th>
                  <th><?= __('Created') ?></th>
                  <th><?= __('Actions') ?></th>
                </tr>
                <tr v-for="(index, file) in files" :id="index">
                  <td>
                    <span v-html="$options.filters.icon(file.type+'/'+file.subtype)"></span>
                  </td>
                  <td>
                    {{file.name}}
                  </td>
                  <td>
                    {{file.size | bytesToMegaBytes | decimal 2 }} MB
                  </td>
                  <td>
                    {{file.created}}
                  </td>
                  <td>
                    <div class="btn-group">
                      <!-- data -->
                      <a class="btn btn-fill btn-xs btn-success" role="button" @click="getFullLink(index)" > <?= __('Show Link') ?> </a>
                      <a v-if="!isSelected(file.id)" href="#" class="btn btn-xs btn-fill btn-info" role="button" @click="add(index)" >Ajouter</a>
                      <a v-if="isSelected(file.id)" href="#" class="btn btn-xs btn-fill btn-warning" role="button" @click="remove(file.id)" >Enlever</a>
                    </div>
                  </td>

                </tr>
              </table>
            </div>

            <!-- thumb option -->
            <div v-if="!listStyle"  v-for="(file, index) in files" :id="index"  class="attachment-files__item col-6 col-sm-4 col-md-3 col-lg-2">
              <div class="card mb-4" >

                <!-- thumb -->
                <attachment-thumb :url="settings.url" :file="file"></attachment-thumb>

                <div class="card-body">
                  <p class="card-text small">
                    {{file.name | truncate(15) }}<br/>
                    {{file.size | bytesToMegaBytes | decimal(2) }} MB<br/>
                  </p>

                  <!-- data -->

                  <a v-if="from == 'input'" v-show="!isSelected(file.id)" href="#" class="btn btn-xs btn-fill btn-info" role="button" @click="add(index);"  ><?= __d('Attachment','Add') ?></a>
                  <a v-if="from == 'trumbowyg'" v-show="!isSelected(file.id)" href="#" class="btn btn-xs btn-fill btn-info" role="button" @click="trumbAdd(file);"  ><?= __d('Attachment','Add') ?></a>

                  <a v-show="isSelected(file.id)" href="#" class="btn btn-xs btn-fill btn-warning" role="button" @click="remove(file.id)" ><?= __d('Attachment','Remove') ?></a>

                </div>
              </div><!-- end card -->
            </div>
          </div>

          <!-- pagination -->
          <attachment-pagination :pagination="pagination" :callback="getFiles" :settings.sync="settings"></attachment-pagination>

          <p></p>
          <div class="custom-modal-footer">
            <div class="btn-group">
              <button type="button" class="modal-default-button btn btn-fill btn-warning" @click="close()">
                <?= __d('Attachment','Close') ?>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</script>
