<?php
namespace Attachment\Protect;

class AwsSignedUrlsProtection extends BaseProtection
{
  public function verify(): boolean
  {
    return false;
  }

  public function createUrl(string $url): string
  {
    return '';
  }
}
