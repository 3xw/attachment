<?php
namespace Attachment\Filesystem;

use Cake\Core\Configure;
use Attachment\Filesystem\FilesystemRegistry;

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

  public function getFullPath($path)
  {
    return $this->filesystem()->getAdapter()->applyPathPrefix($path);
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

  public function write($path, $contents)
  {
    return $this->filesystem()->write($path, $contents);
  }

  public function writeStream($path, $resource)
  {
    return $this->filesystem()->writeStream($path, $resource);
  }

  public function update($path, $contents)
  {
    return $this->filesystem()->update($path, $contents);
  }

  public function updateStream($path, $resource)
  {
    return $this->filesystem()->updateStream($path, $resource);
  }

  public function put($path, $contents)
  {
    return $this->filesystem()->put($path, $contents);
  }

  public function putStream($path, $resource)
  {
    return $this->filesystem()->putStream($path, $resource);
  }

  public function read($path)
  {
    return $this->filesystem()->read($path);
  }

  public function readStream($path)
  {
    return $this->filesystem()->readStream($path);
  }

  public function has($path)
  {
    return $this->filesystem()->has($path);
  }

  public function delete($file, $force = false)
  {
    if(($force || $this->deleteExisting)) $this->filesystem()->delete($file);
  }

  public function readAndDelete($path)
  {
    return $this->filesystem()->readAndDelete($path);
  }

  public function rename($from, $to)
  {
    return $this->filesystem()->rename($from, $to);
  }

  public function copy($from, $to)
  {
    return $this->filesystem()->copy($from, $to);
  }

  public function getMimetype($path)
  {
    return $this->filesystem()->getMimetype($path);
  }

  public function getTimestamp($path)
  {
    return $this->filesystem()->getTimestamp($path);
  }

  public function getSize($path)
  {
    return $this->filesystem()->getSize($path);
  }

  public function createDir($path)
  {
    return $this->filesystem()->createDir($path);
  }

  public function deleteDir($path)
  {
    return $this->filesystem()->deleteDir($path);
  }

  public function listContents($directory = '', $recursive = false)
  {
    return $this->filesystem()->listContents($directory, $recursive);
  }

  public function getVisibility($path)
  {
    return $this->filesystem()->getVisibility($path);
  }

  public function setVisibility($path, $visibility = 'public')
  {
    return $this->filesystem()->setVisibility($path, $visibility);
  }
}
