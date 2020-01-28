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
  protected function _getUrl()
  {
    if(empty($this->_properties['profile']) || empty($this->_properties['profile'])) return '';

    return ProfileRegistry::retrieve($this->profile)->getUrl($this->path);
  }
  protected function _getThumbParams()
  {
    if(empty($this->_properties['profile']) || empty($this->_properties['profile'])) return '';

    $url = ProfileRegistry::retrieve($this->profile)->thumbProfile()->getUrl($this->path);
    return strrpos($url, '?') === false ? '': substr($url, strrpos($url, '?'));
  }
  protected function _getFullpath()
  {
    if(empty($this->_properties['profile']) || empty($this->_properties['profile'])) return '';
    
    return ProfileRegistry::retrieve($this->profile)->getFullPath($this->path);
  }
  protected function _getMime()
  {
    return $this->_properties['type'].'/'.$this->_properties['subtype'];
  }
}
