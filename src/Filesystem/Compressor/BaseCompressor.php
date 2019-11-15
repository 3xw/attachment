<?php
namespace Attachment\Filesystem\Compressor;

use Cake\Core\InstanceConfigTrait;
use Cake\Datasource\ModelAwareTrait;
use Attachment\Model\Entity\Aarchive;
use Cake\Event\Event;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Exception\BadRequestException;

abstract class BaseCompressor
{
  use InstanceConfigTrait;
  use ModelAwareTrait;

  public $Attachments;
  public $attachments = [];

  protected $_defaultConfig = [
    'profile' => 'default'
  ];

  public function __construct(array $config = [])
  {
    $this->setConfig($config);
    $this->Attachments = $this->loadModel('Attachment.Attachments');
  }

  protected function gatherAttachments(Aarchive $entity):bool
  {
    // entity setup
    $entity->set('state', 'PROCESSING');

    // get Attachments
    $this->attachments = $this->Attachments->find()
    ->where([
      'Attachments.id IN' => json_decode($entity->get('aids'))
    ])
    ->toArray();

    return !empty($this->attachments) && !$entity->hasErrors();
  }

  public function compress(Aarchive $entity): bool
  {
    return true;
  }

  public function beforeSave(Event $event): void
  {
    $entity = $event->getSubject()->entity;

    // get attachments
    if(!$this->gatherAttachments($entity)) throw new NotFoundException("Unable to find attachment(s)");

    // compress
    if(!$this->compress($entity)) throw new BadRequestException("Unable to create archive");
  }

  public function afterSave(Event $event): void
  {
    //if(!$event->getSubject()->success) throw new \Exception("Error Processing Request", 1);
  }
}
