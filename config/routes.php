<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

// set thumbnails route
Router::connect('/thumbnails/*', ['plugin' => 'Attachment', 'controller' => 'Resize', 'action' => 'proceed']);
Router::connect('/file/*', ['plugin' => 'Attachment', 'controller' => 'File', 'action' => 'get']);


// set plugin stuff : )
Router::plugin(
    'Attachment',
    ['path' => '/attachment'],
    function (RouteBuilder $routes)
    {
      // protect from direct access
      $routes->redirect('/resize/*', '/');

      $routes->setExtensions(['json']);
      $routes->fallbacks('DashedRoute');
    }
);
