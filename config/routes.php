<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin(
    'Attachment',
    ['path' => '/attachment'],
    function (RouteBuilder $routes) {
      $routes->extensions(['json']);
      $routes->fallbacks('DashedRoute');
    }
);

Router::connect('/images/*', ['plugin' => 'Attachment', 'controller' => 'Resize', 'action' => 'proceed']);
