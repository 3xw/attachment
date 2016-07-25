<?php
// load
use Cake\Core\Configure;
use Cake\Filesystem\Folder;


// check for js file
/*
$frontFolderPath = WWW_ROOT . 'js/vendor/awallef/attachment';
$pluginFolderPath = ROOT . DS . 'plugins' . DS . 'Attachment/webroot/js';
$folder = new Folder($pluginFolderPath);
$folder->copy($frontFolderPath);
*/

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
  'Attachment.vendor/TimSchlechter/bootstrap-tagsinput/bootstrap-tagsinput.css',
],['block' => 'css']);

// add js scripts
$this->Html->script([
  'Attachment.vendor/TimSchlechter/bootstrap-tagsinput/bootstrap-tagsinput.js',
  'Attachment.vendor/twitter/typeahead.js/typeahead.bundle.min.js',
  'Attachment.vendor/rubaxa/Sortable/Sortable.js',
  'Attachment.add-edit.js'
],['block' => 'script']);
