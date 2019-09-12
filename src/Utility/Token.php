<?php
namespace Attachment\Utility;

use Cake\Utility\Text;
use Cake\Http\Session;
use Firebase\JWT\JWT;

class Token
{

  public function encode($obj)
  {
    return JWT::encode($obj, self::key());
  }

  public function decode($cypher)
  {
    try {
      return JWT::decode($cypher, self::key(),['HS256']);
    } catch (Exception $e) {
      throw $e;
    }
  }

  public static function key()
  {
    $session = new Session();
    if(empty($session->read('Attachment.download.key'))) $session->write('Attachment.download.key', Text::uuid());
    return $session->read('Attachment.download.key');
  }
}
