<?php

return [
  'Attachment' => [

    // set profiles
    'profiles' => [
      'default' => [
    		'adapter' => 'League\Flysystem\Adapter\Local',
    		'client' => new League\Flysystem\Adapter\Local('files'),
        'baseUrl' =>  '/files/'
    	],
      'external' => [
    		'adapter' => 'Attachment\Fly\ExternalAdapter',
    		'client' => new Attachment\Fly\ExternalAdapter(),
        'baseUrl' =>  ''
    	],
      'cache' => [
    		'adapter' => 'League\Flysystem\Adapter\Local',
    		'client' => new League\Flysystem\Adapter\Local('thumbnails'),
        'baseUrl' =>  '/thumbnails/'
    	],
    ],

    // upload settings
    'upload' => [
      'maxsize' => 30, // 30MB
      'types' =>[],
      'atags' => [],
      'relation' => 'belongsToMany',
      'profile' => 'default',
      'visibility' => 'public',
      'speech' => false,
      'restrictions' => [] // or Attachment\View\Helper\AttachmentHelper::TAG_RESTRICTED
    ],

    // thumbnails settings
    'thumbnails' => [
      'driver' => 'Imagick', // or Imagick if installed,
      'widths' => [],
      'heights' => [],
      'aligns' => [], // or some of following [0,1,2,3,4,5,6,7,8] with 0 center, 1 top, 4 left, 5 right top corner, 8 left top corner ....
      'crops' => []
    ]
]];
