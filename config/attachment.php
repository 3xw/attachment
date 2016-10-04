<?php

return [
  'Attachment' => [

    // set profiles
    'profiles' => [
      'default' => [
    		'adapter' => 'League\Flysystem\Adapter\Local',
    		'client' => new League\Flysystem\Adapter\Local('files')
    	],
      'cache' => [
    		'adapter' => 'League\Flysystem\Adapter\Local',
    		'client' => new League\Flysystem\Adapter\Local('images')
    	],
    ],

    // upload settings
    'upload' => [
      'maxsize' => 30, // 30MB
      'types' =>['image/jpeg','image/png','image/gif','application/pdf'],
      'atags' => ['file'],
      'relation' => 'belongsToMany',
      'profile' => 'default',
    ],

    // thumbnails settings
    'thumbnails' => [
      'driver' => 'GD', // or Imagick if installed,
      'widths' => ['678','1200'],
      'heights' => false,
      'aligns' => false, // or some of following [0,1,2,3,4,5,6,7,8] with 0 center, 1 top, 4 left, 5 right top corner, 8 left top corner ....
      'crops' => ['16:9','4:3']
    ]
]];
