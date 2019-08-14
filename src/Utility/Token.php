<?php
namespace Attachment\Utility;

use Cake\Utility\Text;
use Cake\Utility\Security;
use Cake\Http\Session;
use Cake\Routing\Router;
use Firebase\JWT\JWT;

class Token {

  protected $_key = null;
  protected $_session = null;
  protected $_uri = null;

  public function encode($string)
  {
    return JWT::encode($string, $this->key());
  }

  public function decode($cypher)
  {
    try {
      $string = JWT::decode($cypher, $this->key(),['HS256']);
      return $string;
    } catch (Exception $e) {
      throw $e;
    }
  }

  public function url($attachment)
  {
    return $this->uri().'/'.$this->encode($attachment->md5);
  }

  public function uri()
  {
    if(!$this->_uri)
      $this->_uri = Router::url(['controller' => 'Download','action' => 'proceed','plugin' => 'attachment', 'prefix' => false],true);
    return $this->_uri;
  }


  public function session()
  {
    if(!$this->_session)
      $this->_session = new Session();
    return $this->_session;
  }

  public function key()
  {
    if(empty($this->session()->read('Attachment.download.key')))
      $this->session()->write('Attachment.download.key', Text::uuid());
    return $this->session()->read('Attachment.download.key');
  }
}
