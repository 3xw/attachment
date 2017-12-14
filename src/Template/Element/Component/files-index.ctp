<!-- files -->
<script type="text/x-template" id="attachment-files-index">
  <div id="attachment-files-index" data-intro="Liste des vos médias." data-position="top">

    <div class="row" >



      <!-- list option -->
      <div class="col-xs-12">
        <table v-if="listStyle" class="table table-bordered table-striped table-condensed table-hover" >
          <tr>
            <th></th>
            <th><?= __('Name') ?></th>
            <th><?= __('Size') ?></th>
            <th><?= __('Created') ?></th>
            <th><?= __('Actions') ?></th>
          </tr>
          <!-- row -->
          <tr v-for="(index, file) in files" id="{{index}}">
            <td>
              {{{file.type+'/'+file.subtype | icon }}}
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
                <a class="btn btn-fill btn-xs btn-success" target="_blank" role="button" v-bind:href="file.download_link" v-if="settings.actions.indexOf('download') != -1">
                  <?= __('Download') ?>
                </a>
                <button class="btn btn-fill btn-xs btn-success" role="button" v-on:click="$dispatch('show-view-file',index)" v-if="settings.actions.indexOf('view') != -1">
                  <?= __('View') ?>
                </button>
                <button class="btn btn-fill btn-xs btn-info" role="button" v-on:click="$dispatch('show-edit-file',index)" v-if="settings.actions.indexOf('edit') != -1">
                  <?= __('Edit') ?>
                </button>
                <button class="btn btn-fill btn-xs btn-danger" role="button" v-on:click="$dispatch('delete-file',index)" v-if="settings.actions.indexOf('delete') != -1">
                  <?= __('Delete') ?>
                </button>
              </div>
            </td>

          </tr>
        </table>
      </div>

      <!-- thumb option -->
      <div v-if="!listStyle" v-for="(index, file) in files" id="{{index}}"  class="attachment-files__item col-xs-4 col-md-3 col-lg-2">
        <div class="thumbnail" >

          <!-- thumb -->
          <attachment-thumb :url="settings.url" :file="file"></attachment-thumb>

          <div class="caption">

            <!-- infos -->
            {{file.name | truncate 15 }}<br/>
            {{file.size | bytesToMegaBytes | decimal 2 }} MB<br/>

            <!-- buttons -->
            <div class="btn-group">
              <a class="btn btn-fill btn-xs btn-success" target="_blank" role="button" v-bind:href="file.download_link" v-if="settings.actions.indexOf('download') != -1">
                <?= __('Download') ?>
              </a>
              <button class="btn btn-fill btn-xs btn-success" role="button" v-on:click="$dispatch('show-view-file',index)" v-if="settings.actions.indexOf('view') != -1">
                <?= __('View') ?>
              </button>
              <button class="btn btn-fill btn-xs btn-info" role="button" v-on:click="$dispatch('show-edit-file',index)" v-if="settings.actions.indexOf('edit') != -1">
                <?= __('Edit') ?>
              </button>
              <button class="btn btn-fill btn-xs btn-danger" role="button" v-on:click="$dispatch('delete-file',index)" v-if="settings.actions.indexOf('delete') != -1">
                <?= __('Delete') ?>
              </button>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</script>
