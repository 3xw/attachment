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
use Attachment\Fly\Profile;

class FlyBehavior extends Behavior
{

  protected $_defaultConfig = [];

  protected $_file = [];

  protected $_fileToRemove = false;

  protected $_uuid = '';

  protected $_session = null;

  public function initialize(array $config)
  {
    // check for a datafield field (there is no default)
    if (!isset($config['file_field']) || '' === $config['file_field']) throw new Exception('Must specify a field for FileBehavior');
  }

  public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
  {
    $settings = $this->config();
    $field = $settings['file_field'];

    if (!empty($data[$field]) && is_array($data[$field]))
    {
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
      $data['md5'] = md5_file($this->_file['tmp_name']);

      $types = explode('/', $this->_file['type']);
      $data['type'] = $types[0];
      $data['subtype'] = $types[1];
      $data['size'] = $this->_file['size'];

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
    $orginalValues = $entity->extractOriginalChanged([$field,'profile','md5']);

    if (isset($entity->{$field}))
    {
      if (!empty($this->_file))
      {
        // TYPE
        $type = explode('/', $this->_file['type']);
        $subtype = $type[1];
        $type = $type[0];

        // GET CONFIG
        $this->_session = new Session();
        $sessionAttachment = $this->_session->read('Attachment.'.$this->_uuid);
        if(!$sessionAttachment)
        {
          $event->stopPropagation();
          $entity->errors($field,['Attachment keys not found in session! Please pass Attachment settings throught session!']);
        }
        $conf = array_merge($sessionAttachment, $settings);

        // CHECK type
        if (!in_array($this->_file['type'], $conf['types']))
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

        // add image meta
        if(in_array($this->_file['type'], ['image/jpeg','image/png','image/gif']))
        {
          $image_info = getimagesize($this->_file['tmp_name']);
          $image_width = $image_info[0];
          $image_height = $image_info[1];
          $entity->set('width', $image_width);
          $entity->set('height', $image_height);
          unset($img);
        }

        // manage existing file...
        $afterReplace = null;
        if(!empty($orginalValues[$field]))
        {
          $oldProfile = new Profile(empty($orginalValues['profile'])? $conf['profile']: $orginalValues['profile']);
          $oldProfile->delete($orginalValues[$field]);
          $afterReplace = $oldProfile->afterReplace;
        }

        // store
        $profile = new Profile($conf['profile']);
        $entity->set('profile', $profile->name);

        // name & dir
        $name = strtolower( time() . '_' . preg_replace('/[^a-z0-9_.]/i', '', $this->_file['name']) );
        $dir = $this->_resolveDir($conf['dir'],$type,$subtype);

        // if replace on edit in profile
        if(!empty($orginalValues[$field]) && $profile->replaceExisting)
        {
          $name = $orginalValues[$field];
          $dir = false;
        }

        // write
        $profile->store($this->_file['tmp_name'], $name, $dir, $conf['visibility'], $this->_file['type']);

        // set entity
        $entity->{$field} = $dir? $dir.DS.$name: $name;

        // excute callback fct if needed
        if(is_callable($afterReplace) && !empty($orginalValues[$field]) && $profile->replaceExisting) $afterReplace($entity);
      }
    }
  }

  protected function _resolveDir($dir,$type,$subtype)
  {
    if($dir === false || $dir === true) return false;
    return str_replace(
      ['{DS}','{$role}','{$username}','{$year}','{$month}','{$type}','{$subtype}'],
      [DS,$this->_session->read('Auth.User.role'),$this->_session->read('Auth.User.username'),date("Y"),date("m"),$type,$subtype],
      $dir
    );
  }

  public function afterDelete(Event $event, EntityInterface $entity, ArrayObject $options)
  {
    $settings = $this->config();
    $field = $settings['file_field'];
    if(!empty($entity->get($field))) (new Profile($entity->get('profile')))->delete($entity->get($field));
  }

}
