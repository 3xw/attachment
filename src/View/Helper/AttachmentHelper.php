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

  public function downloadLink($attachment)
  {
    return $this->_getToken()->url($attachment);
  }

  public function getVersion()
  {
    if(!$this->_version){
      $string = file_get_contents(APP."../composer.lock");
      $composer = json_decode($string, true);
      foreach($composer['packages'] as $package)
      {
        if($package['name'] == '3xw/attachment'){
          if($package['version'] == 'dev-master'){
            $this->_version = '?version='.time();
          }else{
            $this->_version = '?version='.$package['version'];
          }
          break;
        }
      }
    }
    return $this->_version;
  }

  private function _setupIndexComponent()
  {
    $this->_inputComponentCount++;
    if($this->_inputComponentCount == 1)
    {
      // session clear
      $this->request->session()->write('Attachment', '');

      // add template
      $this->_View->append('template', $this->_View->element('Attachment.Component/thumb'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/files-index'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/edit'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/view'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/atags'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/upload'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/embed'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/pagination'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/filters'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/index'));

      // add css
      $this->Html->css([
        'Attachment.vendor/TimSchlechter/bootstrap-tagsinput/bootstrap-tagsinput.css',
        'Attachment.attachment.css'.$this->getVersion(),
      ],['block' => 'css']);

      // add script
      $this->Html->script([
        'Attachment.vendor/TimSchlechter/bootstrap-tagsinput/bootstrap-tagsinput.js',
        'Attachment.vendor/twitter/typeahead.js/typeahead.bundle.min.js',
        'Attachment.vendor/rubaxa/Sortable/Sortable.js',
        'Attachment.Element/Component/utils.js'.$this->getVersion(),
        'Attachment.Element/Component/thumb.js'.$this->getVersion(),
        'Attachment.Element/Component/files-index.js'.$this->getVersion(),
        'Attachment.Element/Component/atags.js'.$this->getVersion(),
        'Attachment.Element/Component/edit.js'.$this->getVersion(),
        'Attachment.Element/Component/view.js'.$this->getVersion(),
        'Attachment.Element/Component/upload.js'.$this->getVersion(),
        'Attachment.Element/Component/embed.js'.$this->getVersion(),
        'Attachment.Element/Component/pagination.js'.$this->getVersion(),
        'Attachment.Element/Component/filters.js'.$this->getVersion(),
        'Attachment.Element/Component/index.js'.$this->getVersion()
      ],['block' => true]);
    }
  }

  private function _setupInputComponent()
  {
    $this->_inputComponentCount++;
    if($this->_inputComponentCount == 1)
    {
      // session clear
      $this->request->session()->write('Attachment', '');

      // add template
      $this->_View->append('template', $this->_View->element('Attachment.Component/thumb'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/files'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/pagination'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/filters'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/upload'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/atags'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/embed'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/browse'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/input'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/inline-options'));

      // add css
      $this->Html->css([
        'Attachment.vendor/TimSchlechter/bootstrap-tagsinput/bootstrap-tagsinput.css',
        'Attachment.attachment.css'.$this->getVersion(),
      ],['block' => 'css']);

      // add script
      $this->Html->script([
        'Attachment.vendor/TimSchlechter/bootstrap-tagsinput/bootstrap-tagsinput.js',
        'Attachment.vendor/twitter/typeahead.js/typeahead.bundle.min.js',
        'Attachment.vendor/rubaxa/Sortable/Sortable.js',
        'Attachment.Element/Component/utils.js'.$this->getVersion(),
        'Attachment.Element/Component/thumb.js'.$this->getVersion(),
        'Attachment.Element/Component/files.js'.$this->getVersion(),
        'Attachment.Element/Component/pagination.js'.$this->getVersion(),
        'Attachment.Element/Component/filters.js'.$this->getVersion(),
        'Attachment.Element/Component/atags.js'.$this->getVersion(),
        'Attachment.Element/Component/upload.js'.$this->getVersion(),
        'Attachment.Element/Component/embed.js'.$this->getVersion(),
        'Attachment.Element/Component/browse.js'.$this->getVersion(),
        'Attachment.Element/Component/input.js'.$this->getVersion(),
        'Attachment.Element/Component/inline-options.js'.$this->getVersion()
      ],['block' => true]);
    }
  }

  private function _getSettings($field,$settings)
  {
    $settings = array_merge(Configure::read('Attachment.upload'),$settings);
    $uuid = Text::uuid();
    $this->request->session()->write('Attachment.'.$uuid, $settings);
    $settings['uuid'] = $uuid;
    $settings['url'] = empty($settings['url'])? $this->Url->build('/') : $settings['url'];
    $settings['label'] = empty($settings['label'])? Inflector::humanize($field) : $settings['label'];
    $settings['translate'] = Configure::read('Attachment.translate');
    $settings['i18n'] = [
      'enable' => Configure::read('Attachment.translate'),
      'languages' => Configure::read('I18n.languages'),
      'defaultLocale' => Configure::read('App.defaultLocale')
    ];

    if(empty($settings['thumbBaseUrl']))
    {
      if($cdn = Configure::read('Attachment.profiles.thumbnails.cdn')) $settings['thumbBaseUrl'] = $cdn->getUrl();
      else $settings['thumbBaseUrl'] = $this->Url->build('/thumbnails/');
    }
    return $settings;
  }

  public function buildIndex($settings = [])
  {
    $this->_setupIndexComponent();
    $settings = $this->_getSettings('Index',$settings);

    $settings['actions'] = (empty($settings['actions']))? ['add','edit','delete','view'] : $settings['actions'];
    $settings['attachments'] = [];
    $profiles = Configure::read('Attachment.profiles');
    $settings['baseUrls'] = [];
    foreach($profiles as $key => $value) $settings['baseUrls'][$key] = $value['baseUrl'];
    return "<attachment-index :aid='\"".Text::uuid()."\"' :settings='".htmlspecialchars(json_encode($settings), ENT_QUOTES, 'UTF-8')."' ></attachment-index>";
  }

  public function jsSetup($field,$settings = [])
  {
    $this->_setupInputComponent();
    $settings = $this->_getSettings($field,$settings);

    $settings['field'] = $field;
    $settings['relation'] = 'belongsTo';
    $settings['attachments'] = [];
    $settings['baseUrl'] = Configure::read('Attachment.profiles.'.$settings['profile'].'.baseUrl');
    return $settings;
  }

  public function input($field, $settings = [])
  {
    $this->_setupInputComponent();
    $conf['relation'] = ($field == 'Attachments')? 'belongsToMany' : 'belongsTo';
    $conf['field'] = ($field == 'Attachments')? '' : $field;
    $settings = array_merge($conf,$settings);
    return "<attachment-input :aid='\"".Text::uuid()."\"' :settings='".htmlspecialchars(json_encode($this->_getSettings($field,$settings)), ENT_QUOTES, 'UTF-8')."' ></attachment-input>";
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
    $url = $this->Url->build($url.$profile.'/',true);
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
