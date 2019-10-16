<?php
namespace Attachment\Filesystem\Protect;

use Cake\Http\ServerRequest;

interface ProtectionInterface
{
  public function verify(ServerRequest $reuest): bool;

  public function getSignedUrl(string $url): string;

  public function getAuthParamsAsString(string $url): string;
}
