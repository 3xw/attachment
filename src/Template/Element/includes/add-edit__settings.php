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

if( !isset($settings) )
{
  $attachments = []; $attachemntSettings = Configure::read('Attachment.upload');
}else
{
  $attachments = empty($settings['attachments'])? [] : $settings['attachments'];
  $attachemntSettings = empty($settings['upload']) ?
    Configure::read('Attachment.upload') :
    array_merge( Configure::read('Attachment.upload'),$settings['upload']);
}

// store...
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
