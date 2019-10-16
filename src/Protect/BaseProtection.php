<?php
namespace Attachment\Protect;

use Cake\Core\InstanceConfigTrait;
use Cake\Http\ServerRequest;

abstract class BaseProtection implements ProtectionInterface
{
  use InstanceConfigTrait;

  protected $_defaultConfig = [];

  public function __construct(array $config = [])
  {
    $this->setConfig($config);
  }

  public function verify(ServerRequest $reuest): bool
  {
    return false;
  }

  public function createUrl(string $url): string
  {
    return '';
  }
}
