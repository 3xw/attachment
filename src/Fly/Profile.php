<?php
namespace Attachment\Fly;

use Cake\Core\Configure;
use Attachment\Fly\FilesystemRegistry;

class Profile
{
  public $name = 'profileName';

  public $settings = [];

  public $replaceExisting = false;

  public $deleteExisting = true;

  public $afterReplace = null;

  function __construct($name)
  {
    $this->name = $name;
    if(empty(Configure::read('Attachment.profiles.'.$name))) throw new \Exception('Profile '.$name.' does not exists!');
    $this->settings = array_merge(Configure::read('Attachment.profiles.parent'), Configure::read('Attachment.profiles.'.$name));
    $this->replaceExisting = $this->settings['replace'];
    $this->deleteExisting = $this->settings['delete'];
    $this->afterReplace = $this->settings['afterReplace'];
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
    while(is_resource($stream)) fclose($stream);
  }

  public function delete($file, $force = false)
  {
    if(($force || $this->deleteExisting)) $this->filesystem()->delete($file);
  }

  public function has($path)
  {
    return $this->filesystem()->has($path);
  }

  public function listContents($directory = '', $recursive = false)
  {
    return $this->filesystem()->listContents($directory, $recursive);
  }

  public function getMimetype($path)
  {
    return $this->filesystem()->getMimetype($path);
  }
}
