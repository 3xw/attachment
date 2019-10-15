<?php
namespace Attachment\Protect;

interface ProtectionInterface
{
  public function verify(): boolean;

  public function createUrl(string $url): string;
}
