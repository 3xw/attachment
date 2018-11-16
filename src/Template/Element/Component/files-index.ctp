<!-- files -->
<script type="text/x-template" id="attachment-files-index">
  <div id="attachment-files-index" data-intro="Liste des vos médias." data-position="top">

    <div class="row" >



      <!-- list option -->
      <div class="col-12">
        <table v-if="listStyle" class="table table-bordered table-striped table-condensed table-hover" >
          <tr>
            <th></th>
            <th><?= __('Name') ?></th>
            <th><?= __('Size') ?></th>
            <th><?= __('Created') ?></th>
            <th><?= __('Actions') ?></th>
          </tr>
          <!-- row -->
          <tr v-for="(file, index) in files" :id="index">
            <td>
              <span v-html="$options.filters.icon(file.type+'/'+file.subtype)"></span>
            </td>
            <td>
              {{file.name}}
            </td>
            <td>
              {{file.size | bytesToMegaBytes | decimal(2) }} MB
            </td>
            <td>
              {{file.created}}
            </td>
            <td>
              <div class="btn-group">
                <!-- data -->
                <a class="btn btn-fill btn-xs btn-success" target="_blank" role="button" v-bind:href="file.download_link" v-if="settings.actions.indexOf('download') != -1">
                  <?= __('Download') ?>
                </a>
                <button class="btn btn-fill btn-xs btn-success" role="button" v-on:click="dispatch('show-view-file',aid,index)" v-if="settings.actions.indexOf('view') != -1">
                  <?= __('View') ?>
                </button>
                <button class="btn btn-fill btn-xs btn-info" role="button" v-on:click="dispatch('show-edit-file',aid,index)" v-if="settings.actions.indexOf('edit') != -1">
                  <?= __('Edit') ?>
                </button>
                <button class="btn btn-fill btn-xs btn-danger" role="button" v-on:click="dispatch('delete-file',aid,index)" v-if="settings.actions.indexOf('delete') != -1">
                  <?= __('Delete') ?>
                </button>
              </div>
            </td>

          </tr>
        </table>
      </div>

      <!-- thumb option -->
      <div v-if="!listStyle" v-for="(file, index) in files" :id="index"  class="attachment-files__item col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="card mb-4" >

          <!-- thumb -->
          <attachment-thumb :url="settings.url" :file="file"></attachment-thumb>

          <div class="card-body">

            <!-- infos -->
            <p class="card-text small">
              {{file.title | truncate(15) }}<br/>
              {{file.name | truncate(15) }}<br/>
              {{file.size | bytesToMegaBytes | decimal(2) }} MB<br/>
            </p>

            <!-- buttons -->
            <a class="btn btn-fill btn-xs btn-success" target="_blank" role="button" v-bind:href="file.download_link" v-if="settings.actions.indexOf('download') != -1">
              <?= __('Download') ?>
            </a>
            <button class="btn btn-fill btn-xs btn-success" role="button" v-on:click="dispatch('show-view-file',aid,index)" v-if="settings.actions.indexOf('view') != -1">
              <?= __('View') ?>
            </button>
            <button class="btn btn-fill btn-xs btn-info" role="button" v-on:click="dispatch('show-edit-file',aid,index)" v-if="settings.actions.indexOf('edit') != -1">
              <?= __('Edit') ?>
            </button>
            <button class="btn btn-fill btn-xs btn-danger" role="button" v-on:click="dispatch('delete-file',aid,index)" v-if="settings.actions.indexOf('delete') != -1">
              <?= __('Delete') ?>
            </button>

          </div>
        </div>
      </div>
    </div>
  </div>
</script>
