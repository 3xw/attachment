<?php
declare(strict_types=1);

namespace Attachment\Filesystem;

use Attachment\Filesystem\Profile;
use Attachment\Model\Entity\Attachment;

class Downloader
{
  public $profile;

  function __construct($profileName = 'sys_temp')
  {
    $this->profile = new Profile($profileName);
  }

  // rmdir
  public function downloadZip(array $attachments):string
  {
    // create dir
    $dir = uniqid('folder-download-', true);
    $this->profile->createDir($dir);

    $files = [];
    foreach($attachments as $attachment) $files[] = $this->download($attachment, $dir, true);

    // delete folder and zip file !!
  }

  public function download(Attachment $attachment, $dir = '', $keepName = false): string
  {
    // profile
    $src = new Profile($attachment->profile);

    // resolve dest ...
    $dest = $keepName? strtolower( time() . '_' . preg_replace('/[^a-z0-9_.]/i', '', $attachment->name) ): uniqid('file-download-', true);
    if($keepName && $attachment->type == 'embed') $dest .= '.html';

    if(!empty($dir))
    {
      $this->profile->createDir($dir);
      $dest = $dir.DS.$dest;
    }

    // copy file ...
    $this->profile->put($dest, $attachment->type == 'embed'? $attachment->embed: $src->read($attachment->path));
    $path = $this->profile->getFullPath($dest);

    // delete file when over
    register_shutdown_function(function() use($path) { unlink($path); });

    return $path;
  }
}
