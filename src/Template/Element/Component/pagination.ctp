<!-- pagination -->
<script type="text/x-template" id="attachment-pagination">
  <div id="attachment-pagination">
    <nav>
      <ul class="pagination">
        <li v-if="start" v-bind:class="{ 'disabled': !pagination.has_prev_page}">
          <a href="#" aria-label="First" @click.prevent="changePage(1)">
            <span aria-hidden="true"><?= __d('Attachment','first') ?></span>
          </a>
        </li>
        <li v-if="(pagination.current_page-offset-1) > 1">
          <a href="#" aria-label="More" @click.prevent="changePage(pagination.current_page-offset-1)">
            <span aria-hidden="true">&laquo;{{pagination.current_page-offset-1}}</span>
          </a>
        </li>
        <li v-bind:class="{ 'disabled': !pagination.has_prev_page}">
          <a href="#" aria-label="Previous" @click.prevent="changePage(pagination.current_page - 1)">
            <span aria-hidden="true">&laquo;</span>
          </a>
        </li>
        <li v-for="( num, index ) in array" :class="{'active': num == pagination.current_page}">
          <a href="#" @click.prevent="changePage(num)">{{ num }}</a>
        </li>
        <li v-bind:class="{ 'disabled': !pagination.has_next_page}">
          <a href="#" aria-label="Next" @click.prevent="changePage(pagination.current_page + 1)">
            <span aria-hidden="true">&raquo;</span>
          </a>
        </li>
        <li v-if="(pagination.current_page+offset+1) < pagination.page_count">
          <a href="#" aria-label="More" @click.prevent="changePage(pagination.current_page+offset+1)">
            <span aria-hidden="true">{{pagination.current_page+offset+1}} &raquo;</span>
          </a>
        </li>
        <li  v-if="end" v-bind:class="{ 'disabled': !pagination.has_next_page}">
          <a href="#" aria-label="Last" @click.prevent="changePage(pagination.page_count)">
            <span aria-hidden="true"><?= __d('Attachment','last') ?></span>
          </a>
        </li>
      </ul>
    </nav>
    <?= __d('Attachment','Page') ?> {{pagination.current_page}} <?= __d('Attachment','on') ?> {{pagination.page_count}} (<?= __d('Attachment','total') ?>: {{pagination.count}})
  </div>
</script>
