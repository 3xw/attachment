<?php
namespace Attachment\Model\Filter\Restriction;

use Cake\Network\Session;
use Cake\Routing\Router;
use Cake\Utility\Inflector;

Trait SessionRestrictionTrait {

  public function restrict()
  {
    if(!empty(Router::getRequest()->query['uuid']))
    {
      $session = new Session();
      $settings = $session->read('Attachment.'.Router::getRequest()->query['uuid']);
      if(!empty($settings['restriction']))
      {
        $restriction = '\\Attachment\\Model\\Filter\\Restriction\\' . Inflector::camelize($settings['restriction']);
        if (class_exists($restriction))
        {
          $restriction = new $restriction;
          $restriction->process($this->query(), $settings);
        }
        //debug($this->query());
      }
    }
  }

}
