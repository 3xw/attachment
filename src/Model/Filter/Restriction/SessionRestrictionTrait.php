<?php
namespace Attachment\Model\Filter\Restriction;

use Attachment\Utility\Token;
use Cake\Core\Configure;
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

        // add downlaod link
        if(!empty($settings['actions']) && array_search('download',$settings['actions']))
        {
          if(version_compare(Configure::version(), '3.4.0') == -1 )
          {
            $this->query()-autoFields(true);
          }else{
            $this->query()->enableAutoFields(true);
          }

          // token
          $token = new Token();
          $this->query()->mapReduce(function ($entity, $key, $mapReduce) use ($token){
            $entity->set('download_link',$token->url($entity));
            $mapReduce->emit($entity, $key);
          });
        }

      }else{
        throw new UnauthorizedException(__("Error Processing Request, reload the page or try login again!"));
      }
    }
  }

}
