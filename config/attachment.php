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
      'thumbnails' => [
    		'adapter' => 'League\Flysystem\Adapter\Local',
    		'client' => new League\Flysystem\Adapter\Local(WWW_ROOT.'thumbnails'),
        'baseUrl' =>  '/thumbnails/'
    	],
      'sys_temp' => [
    		'adapter' => 'League\Flysystem\Adapter\Local',
    		'client' => new League\Flysystem\Adapter\Local(sys_get_temp_dir()),
        'baseUrl' =>  null
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
      'cols' => 'col-6 col-sm-4 col-md-3 col-lg-2',
      'relation' => 'belongsToMany',
      'profile' => 'default',
      'visibility' => 'public',
      'speech' => false,
      'restrictions' => [], // or Attachment\View\Helper\AttachmentHelper::TAG_RESTRICTED
      'pagination' => [
        'offset' => 9, // = 10 pages
        'start' => true,
        'end' => true,
      ],

      // trumbowyg settings
      'trumbowyg' => [
        'svgPath' => '/attachment/icons/icons.svg',
        'lang'=>'fr',
        'btnsDef'=> [
          'media'=> [ 'dropdown'=> ['attachment-browse','attachment-upload','noembed'], 'ico'=> 'noembed']
        ],
        'btns'=> [
          ['viewHTML'],['media'],['formatting'],'btnGrp-semantic',['superscript', 'subscript'],
          ['link'],'btnGrp-justify','btnGrp-lists',['horizontalRule'],['removeformat'],['foreColor', 'backColor'],['fullscreen']
        ],
        'resetCss' => true, 'removeformatPasted'=> true, 'autogrow'=> true,
        'imageOptions' => [
          'align' => [
            '' => 'ne pas toucher',
            'img-float-left' => 'collé à gauche',
            'img-float-right' => 'collé à droite',
            'img-center' => 'centré'
          ],
          'classes' => true, 'altTitle' => true, 'width' => true, 'crop' => true
        ]
      ]
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
