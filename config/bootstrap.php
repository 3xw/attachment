<?php
use Cake\Core\Configure;

Configure::load('Attachment.attachment');

$trumbowyg = Configure::read('Attachment.upload.trumbowyg');
Configure::delete('Attachment.upload.trumbowyg');

collection((array)Configure::read('Attachment.config'))->each(function ($file) {
    Configure::load($file,'default',true);
});
if(Configure::check('Attachment.upload.trumbowyg'))
{
  if(Configure::check('Attachment.upload.trumbowyg.btnsDef'))
  {
    unset($trumbowyg['btnsDef']);
  }
  if(Configure::check('Attachment.upload.trumbowyg.btns'))
  {
    unset($trumbowyg['btns']);
  }
  Configure::write('Attachment.upload.trumbowyg',array_merge($trumbowyg, Configure::read('Attachment.upload.trumbowyg')));
}else
{
  Configure::write('Attachment.upload.trumbowyg',$trumbowyg);
}
//debug(Configure::read('Attachment'));
