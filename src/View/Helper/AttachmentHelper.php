<?php
namespace Attachment\View\Helper;

use Cake\View\Helper;
use Cake\View\View;
use Attachment\Fly\FilesystemRegistry;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Utility\Text;
use Cake\Utility\Inflector;

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

  public function getVersion()
  {
    if(!$this->_version){
      $string = file_get_contents(APP."../composer.lock");
      $composer = json_decode($string, true);
      foreach($composer['packages'] as $package)
      {
        if($package['name'] == '3xw/attachment'){
          $this->_version = '?version='.$package['version'];
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

  public function buildIndex($settings = [])
  {
    $this->_setupIndexComponent();
    $settings['actions'] = (empty($settings['actions']))? ['add','edit','delete','view'] : $settings['actions'];
    $settings['attachments'] = [];
    $settings = array_merge(Configure::read('Attachment.upload'),$settings);
    $uuid = Text::uuid();
    $this->request->session()->write('Attachment.'.$uuid, $settings);
    $settings['uuid'] = $uuid;
    $settings['url'] = $this->Url->build('/', true);
    $settings['translate'] = Configure::read('Attachment.translate');
    $settings['i18n'] = [
      'enable' => Configure::read('Attachment.translate'),
      'languages' => Configure::read('I18n.languages'),
      'defaultLocale' => Configure::read('App.defaultLocale')
    ];
    $profiles = Configure::read('Attachment.profiles');
    $settings['baseUrls'] = [];
    foreach($profiles as $key => $value){
      $settings['baseUrls'][$key] = $value['baseUrl'];
    }
    return "<attachment-index :settings='".htmlspecialchars(json_encode($settings), ENT_QUOTES, 'UTF-8')."' ></attachment-index>";
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
      $this->_View->append('template', $this->_View->element('Attachment.Component/embed'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/browse'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/input'));

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
        'Attachment.Element/Component/upload.js'.$this->getVersion(),
        'Attachment.Element/Component/embed.js'.$this->getVersion(),
        'Attachment.Element/Component/browse.js'.$this->getVersion(),
        'Attachment.Element/Component/input.js'.$this->getVersion()
      ],['block' => true]);
    }
  }

  private function _createTrumbowygUrl($value, $plugins, &$js, &$css)
  {
    if(is_string($value)){
      if($value == 'foreColor' || $value == 'backColor'){ $value = 'colors'; }
      if($value == 'colors'){$css[] = 'https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.6.0/plugins/colors/ui/trumbowyg.colors.min.css';}
      if(array_search($value, $plugins) !== false ){
        $js[] = "https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.6.0/plugins/$value/trumbowyg.$value.min.js";
      }
    }

    if(is_array($value)){
      foreach($value as $v ){
        $this->_createTrumbowygUrl($v, $plugins, $js, $css);
      }
    }
  }

  private function _setTrumbowygComponent($settings)
  {
    $this->_View->append('template', $this->_View->element('Attachment.Component/trumbowyg-options'));
    $this->_View->append('template', $this->_View->element('Attachment.Component/trumbowyg'));
    $css = ['https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.6.0/ui/trumbowyg.min.css'];
    $js = [
      'https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.6.0/trumbowyg.min.js',
      'Attachment.Element/Component/trumbowyg-plugin-upload.js'.$this->getVersion(),
      'Attachment.Element/Component/trumbowyg-plugin-browse.js'.$this->getVersion(),
      'Attachment.Element/Component/trumbowyg.js'.$this->getVersion(),
      'Attachment.Element/Component/trumbowyg-options.js'.$this->getVersion()
    ];

    $plugins = ['base64','cleanpaste','colors','emoji','insertaudio','noembed','pasteimage','preformatted','table','template'];

    foreach($settings['trumbowyg']['btns'] as $btn){ $this->_createTrumbowygUrl($btn, $plugins, $js, $css); }
    if(!empty($settings['trumbowyg']['btnsDef'])){
      foreach($settings['trumbowyg']['btnsDef'] as $btn){
        if(array_key_exists('dropdown', $btn)){$this->_createTrumbowygUrl($btn['dropdown'], $plugins, $js, $css);}
      }
    }

    // add css
    $this->Html->css($css,['block' => 'css']);

    // add script
    $this->Html->script($js,['block' => true]);
  }

  private function _getSettings($field,$settings)
  {
    $settings = array_merge(Configure::read('Attachment.upload'),$settings);
    $uuid = Text::uuid();
    $this->request->session()->write('Attachment.'.$uuid, $settings);
    $settings['uuid'] = $uuid;
    $settings['url'] = $this->Url->build('/');
    $settings['label'] = empty($settings['label'])? Inflector::humanize($field) : $settings['label'];
    $settings['i18n'] = [
      'enable' => Configure::read('Attachment.translate'),
      'languages' => Configure::read('I18n.languages'),
      'defaultLocale' => Configure::read('App.defaultLocale')
    ];
    return $settings;
  }

  public function trumbowyg($field, $settings = [])
  {
    $this->_setupInputComponent();
    $settings = $this->_getSettings($field,$settings);
    $settings['field'] = $field;
    $settings['relation'] = 'belongsTo';
    $settings['trumbowyg']['svgPath'] = $this->Url->build($settings['trumbowyg']['svgPath'], true);
    $settings['attachments'] = [];
    $this->_setTrumbowygComponent($settings);

    return "<attachment-trumbowyg :settings='".htmlspecialchars(json_encode($settings), ENT_QUOTES, 'UTF-8')."' ></attachment-trumbowyg>";
  }

  public function input($field, $settings = [])
  {
    $settings['relation'] = ($field == 'Attachments')? 'belongsToMany' : 'belongsTo';
    $settings['field'] = ($field == 'Attachments')? '' : $field;
    $this->_setupInputComponent();

    return "<attachment-input :settings='".htmlspecialchars(json_encode($this->_getSettings($field,$settings)), ENT_QUOTES, 'UTF-8')."' ></attachment-input>";
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
    $url = $this->Url->build('/thumbnails/'.$profile.'/',true);
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
