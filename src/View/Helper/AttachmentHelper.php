<?php
namespace Attachment\View\Helper;

use Cake\View\Helper;
use Attachment\Model\FilesystemRegistry;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Utility\Text;
use Cake\Utility\Inflector;

class AttachmentHelper extends Helper
{
  public $helpers = ['Url','Html'];

  private $_filesystems = [];

  private $_inputComponentCount = 0;

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
      $this->_View->append('template', $this->_View->element('Attachment.Component/browse'));
      $this->_View->append('template', $this->_View->element('Attachment.Component/input'));

      // add css
      $this->Html->css([
        'Attachment.vendor/TimSchlechter/bootstrap-tagsinput/bootstrap-tagsinput.css',
        'Attachment.attachment.css?v='.time(),
      ],['block' => 'css']);

      // add script
      $this->Html->script([
        'Attachment.vendor/TimSchlechter/bootstrap-tagsinput/bootstrap-tagsinput.js',
        'Attachment.vendor/twitter/typeahead.js/typeahead.bundle.min.js',
        'Attachment.vendor/rubaxa/Sortable/Sortable.js',
        'Attachment.Element/Component/utils.js?v='.time(),
        'Attachment.Element/Component/thumb.js?v='.time(),
        'Attachment.Element/Component/files.js?v='.time(),
        'Attachment.Element/Component/pagination.js?v='.time(),
        'Attachment.Element/Component/filters.js?v='.time(),
        'Attachment.Element/Component/upload.js?v='.time(),
        'Attachment.Element/Component/browse.js?v='.time(),
        'Attachment.Element/Component/input.js?v='.time()
      ],['block' => true]);
    }
  }

  public function input($field, $settings = [])
  {
    $settings['relation'] = ($field == 'Attachment')? 'belongsToMany' : 'belongsTo';
    $settings['field'] = ($field == 'Attachment')? '' : $field;
    $this->_setupInputComponent();
    $settings = array_merge(Configure::read('Attachment.upload'),$settings);
    $uuid = Text::uuid();
    $this->request->session()->write('Attachment.'.$uuid, $settings);
    $settings['uuid'] = $uuid;
    $settings['url'] = $this->Url->build('/', true);
    $settings['label'] = empty($settings['label'])? Inflector::humanize($field) : $settings['label'];

    return "<attachment-input :settings='".json_encode($settings)."' ></attachment-input>";
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
