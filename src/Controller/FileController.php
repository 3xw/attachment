<?php
namespace Attachment\Controller;

use Attachment\Controller\AppController;
use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Attachment\Fly\FilesystemRegistry;

/**
* Attachments Controller
*
* @property \Attachment\Model\Table\AttachmentsTable $Attachments
*/
class FileController extends AppController
{
  private function _filesystem($profile)
  {
    return FilesystemRegistry::retrieve($profile);
  }

  public function get($profile, ...$file )
  {

    // test profile
    if(!Configure::check('Attachment.profiles.'.$profile) || $profile == 'thumbnails' ){ throw new NotFoundException(); }

    // if empty $image
    if(empty($file)){ throw new NotFoundException(); }
    $file = implode("/", $file); 

    //test if found
    if(!$this->_filesystem($profile)->has($file)) throw new NotFoundException();

    // check if baseUrl file
    if(!Configure::read('Attachment.profiles.'.$profile.'.baseUrl')) throw new NotFoundException();

    return $this->redirect(Configure::read('Attachment.profiles.'.$profile.'.baseUrl').$file);

  }
}
