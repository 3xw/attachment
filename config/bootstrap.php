<?php
use Cake\Core\Configure;

/* default:
****************************/
Configure::write('Storage.default_settings', array(

	'fileEngine' => 'local',

	'base'=>'files',

	'maxsize' => 8, // 30MB

	'path' => '{$modelName}{DS}{$year}',//'{$modelName}{DS}{$group}{DS}{$user}{DS}{$year}{DS}{$month}{DS}{$type}{DS}{$subtype}',

	'types' => array(

		'image/jpeg',
		'image/png',
		'image/gif',

		'application/pdf',

		'video/mp4',
		'video/ogg',
		'audio/ogg',
		'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
	),

	'delete' => true

));
