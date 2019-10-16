<?php

return [
  'Attachment' => [

    'listeners' => [],

    // set profiles
    'profiles' => [

      // packed profiles
      'default' => [
    		'client' => new League\Flysystem\Adapter\Local(WWW_ROOT.'files'),
        'baseUrl' =>  '/files/'
    	],
      'img' => [
    		'client' => new League\Flysystem\Adapter\Local(WWW_ROOT.'img'),
        'baseUrl' =>  '/img/'
    	],
      'external' => [
    		'adapter' => 'Attachment\Filesystem\Adapter\External',
        'baseUrl' =>  null,
    	],
      'thumbnails' => [
    		'client' => new League\Flysystem\Adapter\Local(WWW_ROOT.'thumbnails'),
        'baseUrl' =>  '/thumbnails/'
    	],
      'sys_temp' => [
    		'client' => new League\Flysystem\Adapter\Local(sys_get_temp_dir()),
        'baseUrl' =>  null,
    	],
    ],

    // unique
    'md5Unique' => true,

    // translate
    'translate' => false,

    // upload settings
    'upload' => [
      'dir' => false,
      'maxsize' => 30, // 30MB,
      'maxquantity' => -1,
      'types' =>[],
      'atags' => [],
      'atagsDisplay' => false, // false | 'select' | 'input'
      'restrictions' => [], // or Attachment\View\Helper\AttachmentHelper::TAG_RESTRICTED
      'cols' => 'col-6 col-sm-4 col-md-3 col-lg-2',
      'relation' => 'belongsToMany',
      'profile' => 'default',
      'visibility' => 'public',
      'speech' => false,
      'pagination' => [
        'offset' => 9, // = 10 pages
        'start' => true,
        'end' => true,
      ],
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
