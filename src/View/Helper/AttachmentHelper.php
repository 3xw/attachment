<?php
namespace Attachment\View\Helper;

use Cake\View\Helper;
use Attachment\Model\FilesystemRegistry;
use Cake\Core\Configure;
use Cake\Routing\Router;

class AttachmentHelper extends Helper
{
  public $helpers = ['Url'];

  private $_filesystems = [];

  public function filesystem($profile)
  {
    if(empty($this->_filesystems[$profile]))
    {
      $this->_filesystems[$profile] = FilesystemRegistry::retrieve($profile);
    }
    return $this->_filesystems[$profile];
  }

  public function fullPath($attachment)
  {
    $baseUrl = Configure::read('Attachment.profiles.'.$attachment['profile'].'.baseUrl');
    $start = substr($baseUrl,0 , 4);
    $baseUrl = ( $start == 'http' )? $baseUrl : Router::url($baseUrl, true);
    return $baseUrl.$attachment['path'];
  }

  public function image($params, $attributes = null ) {
    $src = $this->thumbSrc( $params );
    $html = '<img src="'. $src .'" ';
    $attributes = ( $attributes )? $attributes : array();
    foreach(  $attributes as $attribute => $value ){
      $html.='  '.$attribute.'="'.$value.'"';
    }
    $html .= ' />';
    return $html;
  }

  public function thumbSrc($params) {
    $start = substr($params['image'],0 , 4);
    $params['image'] = ( $start == 'http' )? $params['image'] : $params['image'];
    return $this->Url->build('/image.php').'?'. http_build_query($params);
  }
}
