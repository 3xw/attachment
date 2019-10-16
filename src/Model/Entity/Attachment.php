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

  protected function _getThumbParams()
  {
    return ProfileRegistry::retrieve($this->profile)->getThumbParams($this->path);
  }

  protected function _getUrl()
  {
    return ProfileRegistry::retrieve($this->profile)->getUrl($this->path);
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
