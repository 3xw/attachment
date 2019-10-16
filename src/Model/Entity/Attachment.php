<?php
namespace Attachment\Model\Entity;


use Cake\ORM\Entity;
use Attachment\Filesystem\ProfileRegistry;

class Attachment extends Entity
{
  protected $_accessible = [
    '*' => true,
    'id' => false,
  ];

  protected $_virtual = ['mime','url','thumb_params'];

  protected $fullUrl;
  protected function getfullUrl()
  {
    if (!$this->fullUrl)  $this->fullUrl = ProfileRegistry::retrieve($this->profile)->getUrl($this->path);
    return $this->fullUrl;
  }

  protected function _getUrl()
  {
    return $this->getfullUrl();
  }

  protected function _getThumbParams()
  {
    $url = $this->getfullUrl();
    return strrpos($url, '?') === false ? null: substr($url, strrpos($url, '?') + 1 );
  }

  protected function _getFullpath()
  {
    return ProfileRegistry::retrieve($this->profile)->getFullPath($this->path);
  }

  protected function _getMime()
  {
    return $this->_properties['type'].'/'.$this->_properties['subtype'];
  }
}
