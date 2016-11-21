<!-- input -->
<script type="text/x-template" id="attachment-input">
  <div class="input form-group">
    <label>{{settings.label}}</label>
    <div class="attachment-input">

      <!-- upload -->
      <attachment-upload :settings.sync="settings" ></attachment-upload>

      <!-- browse -->
      <attachment-browse :types="types" :tags="tags" :settings="settings" ></attachment-browse>

      <!-- embed -->
      <attachment-embed :settings.sync="settings" ></attachment-embed>

      <!-- files -->
      <attachment-files :settings.sync="settings" ></attachment-files>

      <p>
        <div class="btn-group" data-intro="Ajouter des médias à l'aide de ces boutons" data-position="right">
          <button type="button" class="btn btn-fill btn-xs btn-info" @click="$children[0].open()">
            <i class="fa fa-cloud-upload" aria-hidden="true"></i>
            <?= __('Téléverser'); ?>
          </button>
          <button type="button" class="btn btn-fill btn-xs btn-info" @click="$children[1].open()">
            <i class="fa fa-cloud" aria-hidden="true"></i>
            <?= __('Parcourir'); ?>
          </button>
          <button v-if="dispalyEmbed()" type="button" class="btn btn-fill btn-xs btn-info" @click="$children[2].open()">
            <i class="fa fa-code" aria-hidden="true"></i>
            <?= __('Ajouter un embed code'); ?>
          </button>
        </div>
      </p>
    </div>
  </div>
</script>
