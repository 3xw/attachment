<?php
namespace Attachment\Http\Cdn;

use Cake\Core\InstanceConfigTrait;

class BaseCdn
{
  use InstanceConfigTrait;

  protected $_defaultConfig = [];
  protected $_url;
  protected $_error;

  public function __construct(array $config = [])
  {
    $this->setConfig($config);
    $this->_url = $this->getConfig('url')? $this->getConfig('url'): '';
  }

  public function getError()
  {
    return $this->_error;
  }

  public function getUrl()
  {
    return $this->_url;
  }

  public function clear(array $paths = [])
  {
    return true;
  }
}
