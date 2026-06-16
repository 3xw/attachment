<?php
namespace Attachment\Model\Behavior;

use ArrayObject;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Exception;

/**
 * Link behavior — stores a plain external URL as a clickable `link` attachment.
 */
class LinkBehavior extends Behavior
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
    if (!isset($config['link_field']) || '' === $config['link_field']
    || !isset($config['file_field']) || '' === $config['file_field']) {
      throw new Exception('Must specify a link_field and a file_field for LinkBehavior');
    }
  }

  public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
  {
    $settings = $this->getConfig();
    $link_field = $settings['link_field'];
    $file_field = $settings['file_field'];

    if (empty($data[$link_field])) {
      return;
    }

    $url = trim($data[$link_field]);

    $data['type'] = 'link';
    $data['subtype'] = parse_url($url, PHP_URL_HOST) ?: 'url';
    $data[$file_field] = $url;
    $data['size'] = 0;
    $data['md5'] = md5($url);
    $data['profile'] = 'external';

    // never persist the transient link field (no such column)
    unset($data[$link_field]);
  }
}
