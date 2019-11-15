<?php
namespace Attachment\Filesystem\Compressor;

use Cake\Event\Event;
use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;
use Cake\Http\Exception\BadRequestException;

class LambdaCompressor extends BaseCompressor
{
  public function afterSave(Event $event): void
  {
    if(!$event->getSubject()->success) return;

    $entity = $event->getSubject()->entity;
    $SnSclient = new SnsClient($this->getConfig('clientSettings'));
    $message = json_encode(array_merge(['id' => $entity->id], $this->getConfig('data')));
    $topic = $this->getConfig('TopicArn');

    // send message
    try {
      $result = $SnSclient->publish([
        'Message' => $message,
        'TopicArn' => $topic,
      ]);
    } catch (AwsException $e)
    {
      // output error message if fails
      throw new BadRequestException($e->getMessage());
    }
  }
}
