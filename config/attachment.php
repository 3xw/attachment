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
      'img' => [
    		'adapter' => 'League\Flysystem\Adapter\Local',
    		'client' => new League\Flysystem\Adapter\Local('img'),
        'baseUrl' =>  '/img/'
    	],
      'external' => [
    		'adapter' => 'Attachment\Fly\ExternalAdapter',
    		'client' => new Attachment\Fly\ExternalAdapter(),
        'baseUrl' =>  ''
    	],
      'cache' => [
    		'adapter' => 'League\Flysystem\Adapter\Local',
    		'client' => new League\Flysystem\Adapter\Local(WWW_ROOT.'thumbnails'),
        'baseUrl' =>  '/thumbnails/'
    	],
    ],

    // unique
    'md5Unique' => true,

    // translate
    'translate' => false,

    // upload settings
    'upload' => [
      'maxsize' => 30, // 30MB
      'types' =>[],
      'atags' => [],
      'cols' => 'col-xs-4 col-md-3 col-lg-2',
      'relation' => 'belongsToMany',
      'profile' => 'default',
      'visibility' => 'public',
      'speech' => false,
      'restrictions' => [] // or Attachment\View\Helper\AttachmentHelper::TAG_RESTRICTED
    ],

    // thumbnails settings
    'thumbnails' => [
      'driver' => 'Imagick', // or Imagick if installed,
      'compression' => [
        'jpegoptim' => '/usr/local/bin/jpegoptim',
        'pngquant' => '/usr/local/bin/pngquant',
        'convert' => '/usr/local/bin/convert',
        'cwebp' => '/usr/local/bin/cwebp',
        'quality' => 25
      ],
      'breakpoints' => [
        'lg' => '(min-width: 1200px)',
        'md' => '(max-width: 1199px)',
        'sm' => '(max-width: 991px)',
        'xs' => '(max-width: 767px)',
      ],
      'widths' => [],
      'heights' => [],
      'aligns' => [], // or some of following [0,1,2,3,4,5,6,7,8] with 0 center, 1 top, 4 left, 5 right top corner, 8 left top corner ....
      'crops' => []
    ]
]];
