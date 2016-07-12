<?php
namespace Attachment\Model\Behavior;

use ArrayObject;
use Cake\Event\Event;
use Exception;
use Cake\Utility\Inflector;
use Cake\Datasource\EntityInterface;
use Cake\Network\Http\Client;

use Cake\ORM\Behavior;
use Cake\ORM\Table;

/**
* Storage behavior
*/
class EmbedBehavior extends Behavior
{

  /**
  * Default configuration.
  *
  * @var array
  */
  protected $_defaultConfig = [];

  /**
  * Build the behaviour
  *
  * @param array $config Passed configuration
  * @return void
  */
  public function initialize(array $config)
  {
    // check for a datafield field (there is no default)
    if (!isset($config['embed_field']) || '' === $config['embed_field']
    || !isset($config['file_field']) || '' === $config['file_field'] ) {
      throw new Exception('Must specify a embed_field and a file_field for EmbedBehavior');
    }
  }

  public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
  {
    $settings = $this->config();
    $embed_field = $settings['embed_field'];
    $file_field = $settings['file_field'];
    if (!empty($data[$embed_field]))
    {
      $type = 'embed';
      $subType = 'other';
      preg_match('~soundcloud~i', $data[$embed_field], $matches);
      if (!empty($matches))
      {
        $subType = 'soundcloud';
      }
      preg_match('~vimeo~i', $data[$embed_field], $matches);
      if (!empty($matches))
      {
        $subType = 'vimeo';
      }
      preg_match('~youtu~i', $data[$embed_field], $matches);
      if (!empty($matches))
      {
        $subType = 'youtube';
      }
      $data['type'] = $type;
      $data['subtype'] = $subType;
      $data['size'] = 0;

      // try to get image from vimeo or youtube
      if ($subType == 'youtube')
      {
        preg_match('~/embed/([0-9a-z_-]+)~i', $data[$embed_field], $matches);
        if (!empty($matches))
        {
          $data[$file_field] = 'http://img.youtube.com/vi/' . $matches[1] . '/0.jpg'; //$matches[1];
        }
      }
      if ($subType == 'vimeo')
      {
        preg_match('~/video/([0-9a-z_-]+)~i', $data[$embed_field], $matches);
        if (!empty($matches))
        {
          $imgid = $matches[1];
          $http = new Client();
          $hash = unserialize( $http->get('http://vimeo.com/api/v2/video/' . $imgid . '.php')->body );
          $data[$file_field] = $hash[0]['thumbnail_medium'];
        }
      }

      // extract only data we need!!
      preg_match('/src="([^"]+)"/', $data[$embed_field], $match);
      $src = $match[1];

      preg_match('/height="([^"]+)"/', $data[$embed_field], $match);
      $height = $match[1];

      preg_match('/width="([^"]+)"/', $data[$embed_field], $match);
      $width = $match[1];

      preg_match('/scrolling="([^"]+)"/', $data[$embed_field], $match);
      $scrolling = ( !empty($match) )? ' scrolling="'.$match[1].'" ' : '';

      $data[$embed_field] = '<iframe src="'.$src.'" width="'.$width.'" height="'.$height.'" '.$scrolling.' frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';

      // md5
      $data['md5'] = md5($data[$embed_field]);
    }
  }
}
