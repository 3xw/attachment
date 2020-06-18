<?php
namespace Attachment\Model\Entity;

use Cake\ORM\Entity;
use Cake\Core\Configure;
use Cake\Routing\Router;

class Attachment extends Entity
{

  protected $_accessible = [
    '*' => true,
    'id' => false,
  ];

  protected $_virtual = ['fullpath','mime'];

  protected function _getFullpath()
  {
    $baseUrl = Configure::read('Attachment.profiles.'.$this->profile.'.baseUrl');
    $start = substr($baseUrl,0 , 4);
    $baseUrl = ( $start == 'http' )? $baseUrl : Router::url($baseUrl, true);
    return $baseUrl.$this->path;
  }

  protected function _getMime()
  {
    if(!empty($this->_properties['type'])) return  $this->_properties['type'].'/'.$this->_properties['subtype'];
    else return '';
  }


}
