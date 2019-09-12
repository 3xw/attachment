<?php
namespace Attachment\Filesystem\Adapter;

use League\Flysystem\Adapter\Ftp;

class FtpWindowsAdapter extends Ftp
{
  public function setVisibility($path, $visibility)
  {
    return compact('path', 'visibility');
  }
}
