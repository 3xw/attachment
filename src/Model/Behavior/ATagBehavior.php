<?php
namespace Attachment\Model\Behavior;

use ArrayObject;
use Cake\Event\Event;
use Exception;
use Cake\Utility\Inflector;
use Cake\Datasource\EntityInterface;

use Cake\ORM\Behavior;
use Cake\ORM\Table;

/**
* Storage behavior
*/
class ATagBehavior extends Behavior
{

  /**
  * Default configuration.
  *
  * @var array
  */
  protected $_defaultConfig = [];

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
    if(!empty($data['atags']))
    {
      $atags = $this->_table->Atags;
      $tags = [];
      foreach($data['atags'] as $tag)
      {
        $query = $atags->find('all', [
          'conditions' => [
            'slug' => Inflector::slug($tag['name'])
          ]
        ]);
        $atag = $query->first();
        if(!empty($atag))
        {
          array_push($tags, $atag->id);
        }else
        {
          $atag = $atags->newEntity();
          $atag = $atags->patchEntity($atag, $tag);
          $atag = $atags->save($atag);
          if($atag)
          {
            array_push($tags, $atag->id);
          }
        }
      }
      if(!empty($tags))
      {
        $data['atags'] = ['_ids' => $tags];
      }else
      {
        unset($data['atags']);
      }
    }
  }
}
