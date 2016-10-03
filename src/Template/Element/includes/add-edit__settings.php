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
$attachemntSettings = isset($settings) ? array_merge( Configure::read('Attachment'),$settings) : Configure::read('Attachment');
$session = $this->request->session();
$session->write('Attachment', $attachemntSettings);

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
