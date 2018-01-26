<?php
namespace Attachment\Model\Behavior;

use ArrayObject;
use Cake\Event\Event;
use Cake\ORM\Table;
use Cake\ORM\Behavior;

/**
* External behavior
*/
class ExternalBehavior extends Behavior
{
  public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
  {

    if (!empty($data['path']) && substr($data['path'], 0, 4) == 'http')
    {
      /*
      $headers = get_headers($data['path'],1);
      if(substr($headers[0], 9, 3) != 200) continue;
      $pathPieces = explode('/',$image['path']);
      array_push($attachments, array_merge($image, [
        'name' => array_pop($pathPieces),
        'type' => explode('/',$headers['Content-Type'])[0],
        'subtype' => explode('/',$headers['Content-Type'])[1],
        'md5' => md5($image['path']),
        'size' => $headers['Content-Length'],
        'date '=> new DateTime($headers['Date']),
        'profile' => 'external',
      ]));
      */

      $data['type'] = 'image';
      $data['subtype'] = 'jpeg';
      $data['size'] = 0;
      $data['name'] = 'coucou';

      // md5
      $data['md5'] = md5($data['path']);
      $data['profile'] = 'external';

    }
  }

}
