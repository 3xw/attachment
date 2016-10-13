<?php
namespace Attachment\Model\Filter\Restriction;

use Cake\Network\Session;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\Network\Exception\UnauthorizedException;

Trait SessionRestrictionTrait {

  public function restrict()
  {
    if(!empty(Router::getRequest()->query['uuid']))
    {
      $session = new Session();
      $settings = $session->read('Attachment.'.Router::getRequest()->query['uuid']);
      if(!empty($settings['restrictions']))
      {
        foreach($settings['restrictions'] as $restriction)
        {
          $restriction = '\\Attachment\\Model\\Filter\\Restriction\\' . Inflector::camelize($restriction);
          if (class_exists($restriction))
          {
            $restriction = new $restriction;
            $restriction->process($this->query(), $settings);
          }
        }
      }else{
        throw new UnauthorizedException(__("Error Processing Request, reload the page or try login again!"));
      }
    }
  }

}
