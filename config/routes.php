<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

// set thumbnails route
Router::connect('/thumbnails/*', ['plugin' => 'Attachment', 'controller' => 'Resize', 'action' => 'proceed']);
// protect from direct access
Router::redirect('/attachment/resize/*', '/');

// set plugin stuff : )
Router::plugin(
    'Attachment',
    ['path' => '/attachment'],
    function (RouteBuilder $routes) {
      $routes->extensions(['json']);
      $routes->fallbacks('DashedRoute');
    }
);
