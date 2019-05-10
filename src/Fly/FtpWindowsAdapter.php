<?php
namespace Attachment\Fly;

use League\Flysystem\Adapter\Ftp;

class FtpWindowsAdapter extends Ftp
{
  public function setVisibility($path, $visibility)
  {
    return compact('path', 'visibility');
  }
}
