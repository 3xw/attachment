<?php
namespace Attachment\ORM\Behavior;

use DateTime;
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
    if(!empty($data['path']) && is_a($data['path'], '\Zend\Diactoros\UploadedFile')) return;

    if (!empty($data['path']) && substr($data['path'], 0, 4) == 'http' && empty($data['md5']))
    {
      // urlencode if needed
      $pathPieces = explode('/',$data['path']);
      $fileName = array_pop($pathPieces);
      $data['path'] = implode('/',$pathPieces).'/';
      $data['path'] .= (strpos($fileName,'%') === false )? urlencode($fileName): $fileName;

      $headers = get_headers($data['path'],1);
      if (!$headers) return;
      // Normalize header keys to handle HTTP/2 lowercase names
      $headers = array_change_key_case($headers, CASE_UPPER);
      if(substr($headers[0], 9, 3) != 200) return;
      $data['meta'] = json_encode($headers);
      $pathPieces = explode('/',$data['path']);
      $data['name'] = empty($data['name'])? urldecode($fileName): $data['name'];
      // Content-Type may be an array (redirect + final response) or missing
      $contentType = $headers['CONTENT-TYPE'] ?? null;
      if (is_array($contentType)) $contentType = end($contentType);
      if (empty($contentType)) {
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $mimeMap = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp', 'pdf' => 'application/pdf', 'svg' => 'image/svg+xml'];
        $contentType = $mimeMap[$ext] ?? 'application/octet-stream';
      }
      $typeParts = explode('/', $contentType);
      $data['type'] = $typeParts[0];
      $data['subtype'] = $typeParts[1] ?? '';
      $data['md5'] = md5($data['path']);
      $data['size'] = $headers['CONTENT-LENGTH'] ?? null;
      $data['date'] = isset($headers['DATE']) ? new DateTime($headers['DATE']) : new DateTime();
      $data['profile'] = 'external';
    }
  }
}
