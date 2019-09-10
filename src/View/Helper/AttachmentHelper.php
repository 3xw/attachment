<?php
namespace Attachment\View\Helper;

use Attachment\Utility\Token;
use Cake\View\Helper;
use Cake\View\View;
use Attachment\Fly\FilesystemRegistry;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Utility\Text;
use Cake\Utility\Inflector;
use Attachment\Http\Cdn\BaseCdn;

class AttachmentHelper extends Helper
{

  const OPEN = 'open';

  const TAG_RESTRICTED = 'tag_restricted';

  const TAG_OR_RESTRICTED = 'tag_or_restricted';

  const TYPES_RESTRICTED = 'types_restricted';

  public $helpers = ['Url','Html'];

  private $_filesystems = [];

  private $_inputComponentCount = 0;

  private $_version = false;

  private $_token;

  protected function _getToken()
  {
    if(!$this->_token)
    $this->_token = new Token();
    return $this->_token;
  }

  public function component($name, $props = [])
  {
    return $this->_View->Html->tag('attachment-loader', '', ['name' => $name, 'props' => json_encode($props)]);
  }

  public function downloadLink($attachment)
  {
    return $this->_getToken()->url($attachment);
  }

  private function _setupComponent()
  {
    $this->_inputComponentCount++;
    if($this->_inputComponentCount == 1)
    {
      // session clear
      $this->_View->getRequest()->getSession()->write('Attachment', '');

      // add css
      $this->Html->css([
        'plugins/attachment/attachment.min.css'
      ],['block' => true]);

      // add script
      $this->Html->script([
        'plugins/attachment/attachment.vendor.min.js',
        'plugins/attachment/attachment.min.js'
      ],['block' => true]);
    }
  }

  private function _getSettings($field,$settings)
  {
    $settings = array_merge(Configure::read('Attachment.upload'),$settings);
    $uuid = Text::uuid();
    $this->_View->getRequest()->getSession()->write('Attachment.'.$uuid, $settings);
    $settings['uuid'] = $uuid;
    $settings['url'] = $this->Url->build('/');
    $settings['label'] = empty($settings['label'])? Inflector::humanize($field) : $settings['label'];
    $settings['translate'] = Configure::read('Attachment.translate');
    $settings['i18n'] = [
      'enable' => Configure::read('Attachment.translate'),
      'languages' => Configure::read('I18n.languages'),
      'defaultLocale' => Configure::read('App.defaultLocale')
    ];
    return $settings;
  }

  public function buildIndex($settings = [])
  {
    $this->_setupComponent();
    $settings = $this->_getSettings('Index',$settings);

    $settings['actions'] = (empty($settings['actions']))? ['add','edit','delete','view'] : $settings['actions'];
    $settings['attachments'] = [];
    $profiles = Configure::read('Attachment.profiles');
    $settings['baseUrls'] = [];
    foreach($profiles as $key => $value) $settings['baseUrls'][$key] = $value['baseUrl'];
    return $this->component('attachment-browse',[
      'aid' => $settings['uuid'],
      ':settings' => $settings
    ]);
  }

  public function jsSetup($field,$settings = [])
  {
    $this->_setupComponent();
    $settings = $this->_getSettings($field,$settings);

    $settings['field'] = $field;
    $settings['relation'] = 'belongsTo';
    $settings['attachments'] = [];
    $settings['baseUrl'] = Configure::read('Attachment.profiles.'.$settings['profile'].'.baseUrl');
    return $settings;
  }

  public function input($field, $settings = [])
  {
    $this->_setupComponent();
    $conf['relation'] = ($field == 'Attachments')? 'belongsToMany' : 'belongsTo';
    $conf['field'] = ($field == 'Attachments')? '' : $field;
    $settings = $this->_getSettings($field, array_merge($conf,$settings));
    return $this->component('attachment-input',[
      'aid' => $settings['uuid'],
      ':settings' => $settings['uuid']
    ]);
  }

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
    $baseUrl = Configure::read('Attachment.profiles.'.$attachment->profile.'.baseUrl');
    $start = substr($baseUrl,0 , 4);
    $baseUrl = ( $start == 'http' )? $baseUrl : Router::url($baseUrl, true);
    return $baseUrl.$attachment->path;
  }

  public function image($params, $attributes = null )
  {
    // src
    $src = $this->thumbSrc( $params );
    $html = '<img src="'. $src .'" ';
    $attributes = ( $attributes )? $attributes : [];
    foreach(  $attributes as $attribute => $value ){
      $html.='  '.$attribute.'="'.$value.'"';
    }
    $html .= ' />';

    // srcset!!
    if(!empty($params['srcset']) && is_array($params['srcset']) && (!empty($params['width']) || !empty($params['height'])))
    {
      // collect data
      $srcsets = $params['srcset'];
      unset($params['srcset']);
      $dim = !empty($params['width'])? 'width': 'height';
      $breakpoints = Configure::read('Attachment.thumbnails.breakpoints');

      //webp
      preg_match_all('/([a-zA-Z0-9_:\/.èüéöàä\$£ç\&%#*+?=,;~-]*\.png$|[a-zA-Z0-9_:\/.èüéöàä\$£ç\&%#*+?=,;~-]*\.PNG$)/', $params['image'], $noWebp, PREG_SET_ORDER);

      foreach($srcsets as $breakpoint => $values)
      {
        // normal
        $srcset = '';
        $newParams = $params;
        foreach($values as $ratio => $value)
        {
          $r = $ratio + 1;
          $newParams[$dim] = $value;
          $srcset .= $this->thumbSrc( $newParams ).' '.$r.'x, ';
        }
        $srcset = substr($srcset,0, -2);
        $type = empty($noWebp)? 'image/jpeg': 'image/png';
        $html = $this->Html->tag('source','',['srcset' => $srcset, 'media' => $breakpoints[$breakpoint], 'type' => $type]).$html;

        // webp
        $srcset = '';
        $newParams = $params;
        if(empty($noWebp))
        {
          foreach($values as $ratio => $value)
          {
            $r = $ratio + 1;
            $newParams[$dim] = $value;
            preg_match_all('/([a-zA-Z0-9_:\/.èüéöàä\$£ç\&%#*+?=,;~-]*)\.([a-zA-Z]{3,4})$/', $params['image'], $img, PREG_SET_ORDER);
            $newParams['image'] = $img[0][1].'.webp';
            $srcset .= $this->thumbSrc( $newParams ).' '.$r.'x, ';
          }
          $srcset = substr($srcset,0, -2);
          $html = $this->Html->tag('source','',['srcset' => $srcset, 'media' => $breakpoints[$breakpoint], 'type' => 'image/webp']).$html;
        }

      }
      $html = $this->Html->tag('picture',$html);

    }

    // normal stuff

    return $html;
  }

  public function thumbSrc($params) {
    $start = substr($params['image'],0 , 4);

    if(( $start == 'http' )){
      $profile = 'external';
    }else{
      $profile = empty($params['profile'])? 'external' : $params['profile'];
    }
    $cdn = Configure::read('Attachment.profiles.thumbnails.cdn');
    $url = ($cdn && $cdn instanceof BaseCdn)?  $cdn->getUrl(): '/thumbnails/';
    $url = $this->Url->build($url.$profile.'/',['fullBase' => true]);
    $dims = ['height' => 'h','width' => 'w','align' => 'a', 'quality' => 'q'];
    foreach($dims as $key => $value){
      if(!empty($params[$key])){
        $url .= $value.$params[$key];
      }
    }
    if(!empty($params['cropratio'])){
      $url .= 'c'.str_replace(':','-',$params['cropratio']);
    }
    return $url.'/'.$params['image'];
  }
}
