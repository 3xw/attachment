<?php
namespace Attachment\Model\Filter;

use Search\Model\Filter\Base;
use Cake\Core\Configure;
use Cake\Http\Session;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\Http\Exception\UnauthorizedException;

class SessionQuerySettings extends Base
{

  public function process():bool
  {
    // security first
    if(empty(Router::getRequest()->getQuery('uuid'))) throw new UnauthorizedException(__d('Attachment','Missing uuid'));
    if(!$settings = (new Session())->read('Attachment.'.Router::getRequest()->getQuery('uuid'))) throw new UnauthorizedException(__d('Attachment','Uuid is not matching any session settings'));

    if(!empty($settings['restrictions']))
    {
      foreach($settings['restrictions'] as $restriction)
      {
        $restriction = '\\Attachment\\Model\\Filter\\Restriction\\' . Inflector::camelize($restriction);
        if (class_exists($restriction)) (new $restriction())->process($this->getQuery(), $settings);
      }
    }

    return true;
  }
}
