<?php
namespace Attachment\Fly;

use Cake\Core\Configure;
use Attachment\Fly\FilesystemRegistry;

class Profile
{
  $name = 'profileName';

  $settings = [];

  $replaceOnEdit = false;

  $deleteExisting = true;

  function __construct($name)
  {
    $this->name = $name;
    if(empty(Configure::read('Attachment.profiles.'.$name))) throw new \Exception('Profile '.$name.' does not exists!');
    $this->settings = array_merge(Configure::read('Attachment.profiles.parent'), Configure::read('Attachment.profiles.'.$name));
    $this->replaceOnEdit = $this->settings['replaceOnEdit'];
    $this->deleteExisting = $this->settings['delete'];
  }

  public function filesystem()
  {
    return FilesystemRegistry::retrieve($this->name);
  }

  public function store($tmp, $name, $dir = false, $visibility = 'public', $mimetype = 'text/plain')
  {
    // resolve dir...
    if(!empty($dir))
    {
      $this->filesystem()->createDir($dir);
      $name = $dir.DS.$name;
    }

    // delete if exists
    if($this->filesystem()->has($name)) $this->delete($name, true);

    // store file
    $stream = fopen($tmp, 'r+');
    $this->filesystem()->writeStream($name, $stream,[
      'visibility' => $visibility,
      'mimetype' => $mimetype
    ]);
    fclose($stream);
  }

  public function delete($file, $force = false)
  {
    if(($force || $this->deleteExisting)) $this->filesystem()->delete($file);
  }
}
