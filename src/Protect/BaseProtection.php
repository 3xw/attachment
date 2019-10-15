<?php
namespace Attachment\Protect;

use Cake\Core\InstanceConfigTrait;

abstract class BaseProtection implements ProtectionInterface
{
  use InstanceConfigTrait;

  protected $_defaultConfig = [];

  public function __construct(array $config = [])
  {
    $this->setConfig($config);
  }

  public function verify(): boolean
  {
    return false;
  }

  public function createUrl(string $url, array $settings = null): string
  {
    return '';
  }
}
