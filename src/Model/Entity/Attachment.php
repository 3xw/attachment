<?php
namespace Attachment\Model\Entity;

use Cake\ORM\Entity;
use Cake\Core\Configure;
use Cake\Routing\Router;

class Attachment extends Entity
{

  /**
  * Fields that can be mass assigned using newEntity() or patchEntity().
  *
  * Note that when '*' is set to true, this allows all unspecified fields to
  * be mass assigned. For security purposes, it is advised to set '*' to false
  * (or remove it), and explicitly make individual fields accessible as needed.
  *
  * @var array
  */
  protected $_accessible = [
    '*' => true,
    'id' => false,
  ];

  protected function _getFullpath()
  {
    $baseUrl = Configure::read('Attachment.profiles.'.$this->profile.'.baseUrl');
    $start = substr($baseUrl,0 , 4);
    $baseUrl = ( $start == 'http' )? $baseUrl : Router::url($baseUrl, true);
    return $baseUrl.$this->path;
  }

  protected function _getUrl()
  {
    return $this->_properties['type'].'/'.$this->_properties['subtype'];
  }


}
