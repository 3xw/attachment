<?php
namespace Attachment\Controller;

use Attachment\Controller\AppController;
use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Attachment\Model\FilesystemRegistry;

/**
* Attachments Controller
*
* @property \Attachment\Model\Table\AttachmentsTable $Attachments
*/
class ResizeController extends AppController
{
  private function _filesystem($profile)
  {
    return FilesystemRegistry::retrieve($profile);
  }

  public function proceed($profile, $dim, ...$image )
  {
    // test profile
    if(!Configure::check('Attachment.profiles.'.$profile)){ throw new NotFoundException(); }

    // test $dim
    preg_match_all('/([a-z])([0-9]*-[0-9]*|[0-9]*)/', $dim, $dims, PREG_SET_ORDER);
    if(empty($dims)){ throw new NotFoundException(); }

    // if empty $images
    if(empty($image)){ throw new NotFoundException(); }
    $image = implode("/", $image);

    // look for image
    if(!$this->_filesystem($profile)->has($image))
    {
      throw new NotFoundException();
    }

    // set dimentions
    for( $i = 0; $i < count($dims); $i++ ){
      switch($dims[$i][1]){
        case 'c': $cropration = str_replace('-',':',$dims[$i][2]); break;
        case 'w': $width = $dims[$i][2]; break;
        case 'h': $height = $dims[$i][2]; break;
      }
    }

    // retrieve image
    $contents = $this->_filesystem($profile)->read($image);
    
  }
}
