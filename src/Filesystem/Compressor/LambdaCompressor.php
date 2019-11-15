<?php
namespace Attachment\Filesystem\Compressor;

use Cake\Core\InstanceConfigTrait;
use Cake\Http\ServerRequest;
use Attachment\Model\Entity\Aarchive;
use Cake\Event\Event;

class LambdaCompressor extends BaseCompressor
{
  use InstanceConfigTrait;

  protected $_defaultConfig = [];

  public function __construct(array $config = [])
  {
    $this->setConfig($config);
  }

  public function beforeSave(Event $event): void
  {
    $event->getSubject()->entity->set('state', 'CREATED');
    if($event->getSubject()->entity->hasErrors()) throw new \Exception("Error Processing Request", 1);
  }

  public function afterSave(Event $event): void
  {
    if(!$event->getSubject()->success) throw new \Exception("Error Processing Request", 1);
  }
}
