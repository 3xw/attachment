# Attachment plugin for CakePHP 3.x
Attachment plugin solves common problems with media, files and embed data.
The goal is to store files where you want ( Dropbox, AWS S3, ... ) and keep a record of it in a table.

Attachment offers both storage layer and database layer as well as frontend and backend solutions for common needs.

It uses [CakePHP 3](https://cakephp.org/), [Flysystem](https://flysystem.thephpleague.com/) and [Intervention Image](http://image.intervention.io/)

Attachment comes packed with following:

	"friendsofcake/crud": "^4.3",
	"friendsofcake/search": "^2.0",
	"wyrihaximus/fly-pie": "^1.1",
	"league/flysystem-aws-s3-v3": "^1.0",
	"intervention/image": "^2.3",
	"cakephp/migrations": "@stable"

## Installation

###Installation.composer

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

		composer require 3xw/attachment:3.4.x

###Installation.boostrap
Load it in config/boostrap.php like so:

	Plugin::load('Attachment', ['bootstrap' => true, 'routes' => true]);

Alternatively you can overload with your own settings (config/attachment.php):

	Configure::write('Attachment.config', ['attachment']);
	Plugin::load('Attachment', ['bootstrap' => true, 'routes' => true]);

###Installation.db

	bin/cake Migrations migrate -p Attachment

a sql file can found at path:

	vendor/3xw/attachment/config/Schema/attachment.sql

###Installation.folders
Create a thumbnails folder with appropriate chmod to enable php to write in it...

	mkdir webroot/thumbnails
	chmod 777 webroot/thumbnails


If you store your files locally, then create a folder according to default settings or your own. For default set as follow:

	mkdir webroot/files
	chmod 777 webroot/files

## BackendDependencies
### BackendDependencies.libs
In order to use backend tools you need to have following libs installed:

javascript:

	jquery >= 1.11 (maybe lower works as well)
	vuejs 1.0.x (tested with 1.0.26)
	vue-resource 0.9.x (tested with 0.9.3)

css:

	bootstrap v3.x (tested with 3.3.5)

### BackendDependencies.html
Vuejs components are nested to a top parent you need to setup.
It requires one extra block (template). Following is easy to achieve.

in your layout.ctp:

	<head>
		...
		<!-- CSS -->
		<?= $this->Html->css([
		    'bootstrap.min.css',
		    'app.css'
	  	]) ?>
		<?= $this->fetch('css') ?>
		...
	</head>
	<body>
		<div id="admin-app" class="wrapper">
			...flash, content goes here...
		</div>

		<!-- TEMPLATES -->
		<?= $this->fetch('template') ?>

		<!-- SCRIPTS -->
		<?= $this->Html->script([
		    'jquery.min.js'
		    'vue.min.js',
		    'vue-resource.min.js',
		    'app.js'
		 ]) ?>
		<?= $this->fetch('script') ?>

	</body>

### BackendDependencies.js
in your app.js

	(function(scope, $, Vue){

		// init vue
		var initViewJs = function()
		{
			Vue.http.interceptors.unshift(function(request, next) {
				next(function(response) {
					if(typeof response.headers['content-type'] != 'undefined') {
					    response.headers['Content-Type'] = response.headers['content-type'];
					}
				});
			});
			var adminApp = new Vue({el: "#admin-app"});
		}

		// your main fct
		var main = function()
		{
			initViewJs();
			//...
		}

		// boostrap
		$(document).ready(main);


	})(window, jQuery, Vue);

Done!

## Settings
Default settings are present at following path: vendor/3xw/attachment/config/attachment.php

feel free to write your own at following path: config/attachment.php

Exemple of settings:

	<?php
	return [
	  'Attachment' => [

	    // set profiles
	    'profiles' => [
	      's3' => [
	        'adapter' => 'League\Flysystem\AwsS3v3\AwsS3Adapter',
	        'client' => new League\Flysystem\AwsS3v3\AwsS3Adapter(Aws\S3\S3Client::factory([
	          'credentials' => [
	            'key'    => '***',
	            'secret' => '***',
	          ],
	          'region' => 'eu-central-1',
	          'version' => 'latest',
	        ]),'s3.example.com',''),
	        'baseUrl' =>  's3.example.com'
	      ],
	    ],

	    // upload settings
	    'upload' => [
	      'maxsize' => 30, // 30MB
	      'types' =>['image/jpeg','image/png','image/gif'],
	      'atags' => [],
	      'profile' => 's3',
	    ],

	    // thumbnails settings
	    'thumbnails' => [
	      'driver' => 'Imagick', // or Imagick if installed,
	      'compression' => [
         		'jpegoptim' => '/usr/local/bin/jpegoptim', // path or false ( default /usr/local/bin/jpegoptim )
        		'pngquant' => '/usr/local/bin/pngquant', // path or false ( default /usr/local/bin/pngquant )
        		'quality' => 25 // encoding quality level from 0 to 100 ( default 25 )
      		],
      		'breakpoints' => [
		        'lg' => '(min-width: 1200px)',
		        'md' => '(max-width: 1199px)',
		        'sm' => '(max-width: 991px)',
		        'xs' => '(max-width: 767px)',
	      ],
	      'widths' => ['678','1200'],
	      'heights' => false,
	      'aligns' => false, // or some of following [0,1,2,3,4,5,6,7,8] with 0 center, 1 top, 4 left, 5 right top corner, 8 left top corner ....
	      'crops' => ['16:9','4:3','1:1']
	    ]
	]];

###Settings.profiles
You can set up your profiles according to [Flysystem](https://flysystem.thephpleague.com/) doc just add baseUrl in order to retrieve full urls. Profiles are stored by name. So you can split your file in sevral systems.

Attachment comes prepact with three default settings:

	default // Local file system stored in webroot/files
	external // used for external urls
	cache // for thumbs creations

following is the default adptater for local storage:

	'default' => [
		'adapter' => 'League\Flysystem\Adapter\Local',
		'client' => new League\Flysystem\Adapter\Local('files'),
    	'baseUrl' =>  '/files/'
	],

So you can use your own or install new one with composer.

###Settings.upload
The upload is made before saving a realted records. global settings are setup under Attachment.upload. You can set global behaviors and then override them local in add.ctp or edit.ctp. Sevral options are avaliable here:

	'upload' => [
      'maxsize' => 30, // 30MB
      'types' =>['image/jpeg','embed/soundcloud',...], // mime types and embed/:service for embed stuff
      'atags' => [], // atags are use to store attachemnts with
      'relation' => 'belongsToMany', // model relation
      'profile' => 'default', // profile to use (where you store files)
      'visibility' => 'public', // public or private
      'speech' => false, // french goody
      'restrictions' => [] // or Attachment\View\Helper\AttachmentHelper::TAG_RESTRICTED
    ],

Restrictions are behaviors used in backend to sort files.

	 AttachmentHelper::TAG_RESTRICTED // enforce attachments to associted with given tags in save and retieve with a AND strategy
	 AttachmentHelper::TAG_OR_RESTRICTED // enforce attachments to associted with given tags in save and retieve with a OR strategy
	 AttachmentHelper::types_restricted // enforce attachments to saved and retrieve with a OR strategy according given mime types

###Settings.thumbnails
Attachment.thumbnails is the settings for thumbs generation.

	'thumbnails' => [
      'driver' => 'Imagick', // or Imagick if installed,
      'widths' => [600, 1200],
      'heights' => [],
      'aligns' => [], // or some of following [0,1,2,3,4,5,6,7,8] with 0 center, 1 top, 4 left, 5 right top corner, 8 left top corner ....
      'crops' => ['4:3','16:9']
    ]

This settings are global and restirct local changes in order to keep logic of thumb in one file and limit extra formats.
each table are possibility you allow. So only 600px and 1200px thumbs are allowed. Only crop of 4:3 and 16:9 are allowed.

## Usage
###Usage.model
Attachment is two tables: Attachments and Atags. So you can bind any of your models with, all relations types are supported.

	$this->belongsToMany('Attachments', [
	  'foreignKey' => 'post_id',
	  'targetForeignKey' => 'attachment_id',
	  'joinTable' => 'attachments_posts'
	]);

	OR

	$this->belongsTo('Attachments', [
      'foreignKey' => 'attachment_id',
      'joinType' => 'INNER' // OR LEFT ...
    ]);

Attachment handles an 'order' field as well. So feel free to add such a field in your HABTM join tables...

###Usage.controller
Simply use contain or any join you need...

	public function index()
	{
		$this->paginate = [
		  'contain' => ['Attachments' /* => ['sort' => 'order'] */ ] // if HABTM with an order field
		];
		$posts = $this->paginate($this->Posts);
		$this->set(compact('posts'));
		$this->set('_serialize', ['posts']);
	}

###Usage.view
All skills are in the Helper Attachment. So first of All add it into your AppView.

in src/View/AppView.php

	public function initialize()
	{
		$this->loadHelper('Attachment.Attachment');
	}

####Usage.view.backend
In add.ctp

	<!-- Attachment -->
	<?= $this->Attachment->input('Attachments', // if Attachments => HABTM else if !Attachments => belongsTo
	  	[
		  	'label' => 'Image',
		  	'types' =>['image/jpeg','image/png'],
		  	'atags' => ['Restricted Tag 1', 'Restricted Tag 2'],
		  	'profile' => 's3', // optional as it was set in config/attachment.php
		  	'cols' => 'col-xs-6 col-md-6 col-lg-4', // optional as it was set in config/attachment.php
		  	'restrictions' => [
		    	Attachment\View\Helper\AttachmentHelper::TAG_RESTRICTED,
		    	Attachment\View\Helper\AttachmentHelper::TYPES_RESTRICTED
		  	],
		  	'attachments' => [] // array of exisiting Attachment entities ( HABTM ) or entity ( belongsTo )
		]
	) ?>

In edit.ctp

	<!-- Attachment -->
	<?= $this->Attachment->input('Attachments', // if Attachments => HABTM else if !Attachments => belongsTo
	  	[
		  	'label' => 'Image',
		  	'types' =>['image/jpeg','image/png'],
		  	'atags' => ['Restricted Tag 1', 'Restricted Tag 2'],
		  	'profile' => 's3', // optional as it was set in config/attachment.php
		  	'cols' => 'col-xs-6 col-md-6 col-lg-4', // optional as it was set in config/attachment.php
		  	'restrictions' => [
		    	Attachment\View\Helper\AttachmentHelper::TAG_RESTRICTED,
		    	Attachment\View\Helper\AttachmentHelper::TYPES_RESTRICTED
		  	],
		  	'attachments' => $posts->attachments // array of exisiting Attachment entities ( HABTM ) or entity ( belongsTo )
		]
	) ?>

Global Attachments index :

	<!-- Attachments element -->
    <?= $this->Attachment->buildIndex([
      'actions' => ['add','edit','delete','view'],
      'types' =>['image/jpeg','image/png','embed/youtube','embed/vimeo'],
      'atags' => ['Restricted Tag 1', 'Restricted Tag 2'],
      'profile' => 's3',
      'restrictions' => [
        Attachment\View\Helper\AttachmentHelper::TAG_RESTRICTED,
        Attachment\View\Helper\AttachmentHelper::TYPES_RESTRICTED
      ]
    ]) ?>

####Usage.view.frontend
in file

	<!-- Display a 16:9 croped image  -->
	<?= $this->Attachment->image([
		'image' => $post->attachments[0]->path,
		'profile' => $post->attachments[0]->profile,
		'width' => '600',
		'cropratio' => '16:9,
		'quality' => 50, // from 0 to 100 ( default 25 in plugin's config file attachment.php )
		'srcset' => [
	      'lg' => [360,720],
	      'md' => [293, 586],
	      'sm' => [283, 566],
	      'xs' => [767,1534],
	    ]
	],['class' => 'img-responsive']) ?>

	<!-- Display an embed video  -->
	<?= $post->attachments[0]->embed ?>
