<?php
namespace Attachment\Protect;

use Cake\Http\ServerRequest;

interface ProtectionInterface
{
  public function verify(ServerRequest $reuest): bool;

  public function createUrl(string $url): string;
}
