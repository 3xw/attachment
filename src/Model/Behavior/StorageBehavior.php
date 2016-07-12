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

  public function getPath($modelName, $path, $type, $subtype, ArrayObject $options)
  {
    //'{$modelName}{DS}{$group}{DS}{$user}{DS}{$year}{DS}{$month}{DS}{$type}{DS}{$subtype}{DS}{$fileName}';

    $modelName = substr($modelName, strrpos($modelName, '\\') + 1 );

    if(!property_exists ( $options, 'user'))
    {
      $group = 'public';
      $user = 'default';
    }else
    {
      $user = $options->user;
      $group = $user['role_id'];
      $user = $user['id'];
    }

    $path = str_replace(array(
      '{$modelName}',
      '{DS}',
      '{$role}',
      '{$user}',
      '{$year}',
      '{$month}',
      '{$type}',
      '{$subtype}'
    ), array(
      Inflector::pluralize($modelName),
      DS,
      $group,
      $user,
      date("Y"),
      date("m"),
      $type,
      $subtype
    ), $path);

    return strtolower($path);
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
        $sessionStorage = $session->read('Storage');
        if(!$sessionStorage){
          $event->stopPropagation();
          $entity->errors($field,['Storage kes not found in session! Please pass Storgae Settings thrught session!']);
        }
        $conf = array_merge($sessionStorage, $settings);

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

        switch ($conf['fileEngine']) {
          case 'local':
            $path = WWW_ROOT . $conf['base'] . DS . $this->getPath(get_class($entity), $conf['path'], $type, $subtype, $options);
            $folder = new Folder();
            $folder->create($path, 0777);

            $entity->{$field} = $conf['base'] . DS . $this->getPath(get_class($entity), $conf['path'], $type, $subtype, $options) . DS . $name;
            if( !move_uploaded_file($temp_name, $path . DS . $name) )
            {
              $event->stopPropagation();
              $entity->errors($field,['Unable to move file on Server HD']);
              return false;
            }
            break;
        }

      }
    }
  }

  public function beforeDelete(Event $event, EntityInterface $entity, ArrayObject $options)
  {
    $settings = $this->config();
    $field = $settings['file_field'];
    $conf = array_merge( Configure::read('Storage.settings'), $settings);

    if (!empty($entity[$field]) && $conf['delete'])
    {
      $this->_fileToRemove = $entity[$field];
    }
  }

  public function afterDelete(Event $event, EntityInterface $entity, ArrayObject $options)
  {
    if ($this->_fileToRemove)
    {
        $conf = array_merge( Configure::read('Storage.settings'), $this->config() );
        switch ($conf['fileEngine'])
        {
            case 'local':
                $file = new File(WWW_ROOT . $this->_fileToRemove);
                return $file->delete();
                break;
        }
    }
  }

}
