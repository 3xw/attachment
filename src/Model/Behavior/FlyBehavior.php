<?php
namespace Attachment\Model\Behavior;

use ArrayObject;
use Cake\Event\Event;
use Exception;
use Cake\Utility\Inflector;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Network\Session;

use Cake\ORM\Behavior;
use Cake\ORM\Table;

use Attachment\Fly\FilesystemRegistry;

/**
* Storage behavior
*/
class FlyBehavior extends Behavior
{

  /**
  * Default configuration.
  *
  * @var array
  */
  protected $_defaultConfig = [];

  protected $_file = [];

  protected $_fileToRemove = false;

  protected $_uuid = '';

  protected $_session = null;

  /**
  * Build the behaviour
  *
  * @param array $config Passed configuration
  * @return void
  */
  public function initialize(array $config)
  {
    // check for a datafield field (there is no default)
    if (!isset($config['file_field']) || '' === $config['file_field']) {
      throw new Exception('Must specify a field for FileBehavior');
    }
  }

  public function filesystem($profile)
  {
    return FilesystemRegistry::retrieve($profile);
  }

  public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
  {
    $settings = $this->config();
    $field = $settings['file_field'];

    if (!empty($data[$field]) && is_array($data[$field])) {

      if ($data[$field]['error'] != UPLOAD_ERR_OK)
      {
        $event->stopPropagation();
        return false;
      }

      // store file!
      $this->_file = $data[$field];

      // set uuid if exists
      $this->_uuid = empty($data['uuid'])? '' : $data['uuid'];

      // md5
      $data['md5'] = md5_file($data[$field]['tmp_name']);

      $types = explode('/', $data[$field]['type']);
      $data['type'] = $types[0];
      $data['subtype'] = $types[1];
      $data['size'] = $data[$field]['size'];

      // date...
      if (!isset($data['date']))
      {
        $data['date'] = date('Y-m-d H:i:s');
      }

      // name of file...
      if (!isset($data['name']) || $data['name'] == '')
      {
        $data['name'] = $data[$field]['name'];
      }
    }
  }

  public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
  {
    $settings = $this->config();
    $field = $settings['file_field'];
    $orginalValues = $entity->extractOriginalChanged([
      'path',
      'profile',
      'md5'
    ]);

    if (isset($entity->{$field}))
    {
      if (!empty($this->_file))
      {
        // NAME
        $name = strtolower( time() . '_' . preg_replace('/[^a-z0-9_.]/i', '', $this->_file['name']) );

        // TEMPNAME
        $temp_name = $this->_file['tmp_name'];

        // TYPE
        $fullType = $this->_file['type'];
        $type = explode('/', $fullType);
        $subtype = $type[1];
        $type = $type[0];

        // GET CONFIG
        $this->_session = new Session();
        $sessionAttachment = $this->_session->read('Attachment.'.$this->_uuid);
        if(!$sessionAttachment){
          $event->stopPropagation();
          $entity->errors($field,['Attachment keys not found in session! Please pass Attachment settings throught session!']);
        }
        $conf = array_merge($sessionAttachment, $settings);

        // CHECK type
        if (( in_array($fullType, $conf['types']) === false))
        {
          $event->stopPropagation();
          $entity->errors($field,['This file type is not suported!']);
          return false;
        }

        // CHECK Size
        if ($conf['maxsize'] * ( 1024 * 1024 ) < $this->_file['size'])
        {
          $event->stopPropagation();
          $entity->errors($field,['This file is too large max size is : '  . ( $conf['maxsize']  ) .' MB']);
          return false;
        }

        // get sizes if file is image...
        if($fullType == 'image/jpeg' || $fullType == 'image/png' || $fullType == 'image/gif')
        {
          $image_info = getimagesize($temp_name);
          $image_width = $image_info[0];
          $image_height = $image_info[1];
          $entity->set('width', $image_width);
          $entity->set('height', $image_height);
          unset($img);
        }

        // store file
        $profile = $conf['profile'];
        $entity->set('profile', $profile);

        // resolve dir...
        if($conf['dir'] !== false && $conf['dir'] !== true)
        {
          $dir = $this->_resolveDir($conf['dir'],$type,$subtype);
          $this->filesystem($profile)->createDir($dir);
          $name = $dir.DS.$name;
        }

        // delete old one exists
        if(!empty($orginalValues['path']))
        {
          $oldProfile = empty($orginalValues['profile'])? $profile: $orginalValues['profile'];
          $this->filesystem($oldProfile)->delete($orginalValues['path']);
        }

        // delete if exists
        if($this->filesystem($profile)->has($name))
        {
          $this->filesystem($profile)->delete($name);
        }

        // store file
        $stream = fopen($temp_name, 'r+');
        $this->filesystem($profile)->writeStream($name, $stream,[
          'visibility' => $conf['visibility'],
          'mimetype' => $fullType
        ]);
        fclose($stream);
        $entity->{$field} = $name;
      }
    }
  }

  protected function _resolveDir($dir,$type,$subtype)
  {
    return str_replace(
      ['{DS}','{$role}','{$username}','{$year}','{$month}','{$type}','{$subtype}'],
      [DS,$this->_session->read('Auth.User.role'),$this->_session->read('Auth.User.username'),date("Y"),date("m"),$type,$subtype],
      $dir
    );
  }

  public function beforeDelete(Event $event, EntityInterface $entity, ArrayObject $options)
  {
    $settings = $this->config();
    $field = $settings['file_field'];
    $delete = $settings['delete'];

    if (!empty($entity[$field]) && $delete)
    {
      $this->_fileToRemove = [
        'file' => $entity[$field],
        'profile' => $entity['profile']
      ];
    }
  }

  public function afterDelete(Event $event, EntityInterface $entity, ArrayObject $options)
  {
    if ($this->_fileToRemove)
    {
      $file = $this->_fileToRemove['file'];
      $profile = $this->_fileToRemove['profile'];
      if($this->filesystem($profile)->has($file))
      {
        $this->filesystem($profile)->delete($file);
      }
    }
  }

}
