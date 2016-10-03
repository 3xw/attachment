<?php

return [
  'Attachment' => [

    // set profiles
    'profiles' => [
      'default' => [
    		'adapter' => 'League\Flysystem\Adapter\Local',
    		'client' => new League\Flysystem\Adapter\Local('files')
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
]];
