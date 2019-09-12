<?php
declare(strict_types=1);

namespace Attachment\Filesystem;

use Attachment\Filesystem\Profile;
use Attachment\Model\Entity\Attachment;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

class Downloader
{
  public $profile;

  function __construct($profileName = 'sys_temp')
  {
    $this->profile = new Profile($profileName);
  }

  // rmdir
  public function downloadZip(array $attachments, $fullPath = '', $keepFile = false):string
  {
    // zip time
    $zipName = uniqid('archive-', true).'.zip';
    $zipPath = empty($fullPath)? sys_get_temp_dir() : $fullPath;
    $zipPath = $zipPath.DS.$zipName;
    $zip = new Filesystem(new ZipArchiveAdapter($zipPath));

    // copy and add to zip
    foreach($attachments as $attachment)
    {
      // profile
      $src = new Profile($attachment->profile);

      // resolve dest ...
      $name = strtolower( time() . '_' . preg_replace('/[^a-z0-9_.]/i', '', $attachment->name) );
      if($attachment->type == 'embed') $name .= '.html';

      // copy file
      $zip->write($name, $attachment->type == 'embed'? $attachment->embed: $src->read($attachment->path));
    }

    // delete zip file !!
    if($keepFile === false) register_shutdown_function(function() use($zipPath) { unlink($zipPath); });

    return $zipPath;
  }

  public function download(Attachment $attachment, $dir = '', $keepName = false, $keepFile = false): string
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
    if($keepFile === false) register_shutdown_function(function() use($path) { unlink($path); });

    return $path;
  }
}
