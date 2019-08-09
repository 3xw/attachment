<?php
namespace Attachment\Controller;

use Attachment\Utility\Token;
use Cake\Network\Exception\ForbiddenException;

use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Attachment\Fly\FilesystemRegistry;
use Cake\Filesystem\Folder;
use Cake\Core\App;
use Attachment\Controller\AppController;

/**
* Attachments Controller
*
* @property \Attachment\Model\Table\AttachmentsTable $Attachments
*/
class DownloadController extends AppController
{
  private function _filesystem($profile)
  {
    return FilesystemRegistry::retrieve($profile);
  }

  public function proceed($token)
  {
    // if empty $token
    if(empty($token)){ throw new ForbiddenException('No token provided'); }

    // test token
    $tokenHelper = new Token;
    $md5 = $tokenHelper->decode($token);

    // look for Attachment
    $this->loadModel('Attachments');
    $attachment = $this->Attachments->find()
    ->where(['md5' => $md5])
    ->first();

    // test profile
    if(!Configure::check('Attachment.profiles.'.$attachment->profile) || $attachment->profile == 'sys_temp' ){ throw new NotFoundException('Profile not found'); }

    // look for media
    if(!$this->_filesystem($attachment->profile)->has($attachment->path))
    {
      throw new NotFoundException('File not found');
    }

    // if embed type
    if($attachment->type == 'embed')
    {
      $this->response->body($attachment->embed);
      $this->response = $this->response->withType('html');
      $this->response = $this->response->withDownload($attachment->name.'.html');
      return $this->response;
    }

    // switch strategy from size
    if($attachment->size < 1048576 * 1) // 1MB
    {
      $this->response->body($this->_filesystem($attachment->profile)->read($attachment->path));
      $this->response->type(['my-type' => $attachment->type.'/'.$attachment->subtype]);
      $this->response = $this->response->withType('my-type');
      $this->response = $this->response->withDownload($attachment->name);
      return $this->response;
    }else
    {
      // copy file
      $filename = uniqid('cakephp-attachment-file-', true);
      $this->_filesystem('sys_temp')->put($filename, $this->_filesystem($attachment->profile)->read($attachment->path));
      $path = $this->_filesystem('sys_temp')->getAdapter()->applyPathPrefix($filename);

      // delete file when over
      register_shutdown_function(function() use($path) { unlink($path); });

      // serve
      $this->response->file($path);
      $this->response->type(['my-type' => $attachment->type.'/'.$attachment->subtype]);
      $this->response = $this->response->withType('my-type');
      $this->response = $this->response->withDownload($attachment->name);
      return $this->response;
    }
  }
}
