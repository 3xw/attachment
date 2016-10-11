<?php
namespace Attachment\Model\Behavior;

use ArrayObject;
use Cake\Event\Event;
use Exception;
use Cake\Utility\Inflector;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Datasource\EntityInterface;
use Cake\Network\Session;

use Cake\ORM\Behavior;
use Cake\ORM\Table;

use Attachment\Model\FilesystemRegistry;

/**
* Storage behavior
*/
class StorageBehavior extends Behavior
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

      if ($data[$field]['error'] != 0)
      {
        unset($data[$field]);
        return;
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
        $session = new Session();
        $sessionAttachment = $session->read('Attachment.'.$this->_uuid);
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

        // store file
        $profile = $conf['profile'];
        $entity->set('profile', $profile);

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
