<?
// load
use Cake\Core\Configure;
use Cake\Filesystem\Folder;

// check for js file
$frontFolderPath = WWW_ROOT . 'js/vendor/awallef/attachment';
$pluginFolderPath = ROOT . DS . 'plugins' . DS . 'Attachment/webroot/js';
$folder = new Folder($pluginFolderPath);
$folder->copy($frontFolderPath);

$attachments = [];

if( isset($settings) ){
  if(!empty($settings['attachments'])){
    $attachments = $settings['attachments'];
    unset($settings['attachments']);
  }
}


// get config and store it in session!
if( Configure::read('Storage.settings') ){
  if( isset($settings) ){
    $attachemntSettings = array_merge( Configure::read('Storage.default_settings'),Configure::read('Storage.settings'),$settings);
  }else{
    $attachemntSettings = array_merge( Configure::read('Storage.default_settings'),Configure::read('Storage.settings'));
  }
}else{
  if( isset($settings) ){
    $attachemntSettings = array_merge( Configure::read('Storage.default_settings'),$settings);
  }else{
    $attachemntSettings = Configure::read('Storage.default_settings');
  }
}
$session = $this->request->session();
$session->write('Storage', $attachemntSettings);

//only for vuejs
$attachemntSettings['addURL'] = $this->Url->build([
  'controller' => 'Attachments',
  'action' => 'add',
  'plugin' => 'Attachment',
  'prefix' => false,
  //'ext' => 'json'
]);
$attachemntSettings['browseURL'] = $this->Url->build([
  'controller' => 'Attachments',
  'action' => 'index',
  'plugin' => 'Attachment',
  'prefix' => false,
  //'ext' => 'json'
]);
$attachemntSettings['tagsURL'] = $this->Url->build([
  'controller' => 'Atags',
  'action' => 'index',
  'plugin' => 'Attachment',
  'prefix' => false,
  //'ext' => 'json'
]);
$attachemntSettings['attachments'] = $attachments;

// add css
$this->Html->css([
  'https://rawgit.com/timschlechter/bootstrap-tagsinput/master/src/bootstrap-tagsinput.css',
],['block' => 'css']);

// add js scripts
$this->Html->script([
  'https://rawgit.com/TimSchlechter/bootstrap-tagsinput/master/src/bootstrap-tagsinput.js',
  'https://rawgit.com/twitter/typeahead.js/master/dist/typeahead.bundle.min.js',
  'http://rubaxa.github.io/Sortable/Sortable.js',
  'vendor/awallef/attachment/add-edit.js'
],['block' => 'script']);
