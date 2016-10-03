<?php
use Cake\Core\Configure;

/* default:
****************************/
Configure::write('Attachment', [
	'default' => [
		'adapter' => 'League\Flysystem\Adapter\Local',
		'client' => new League\Flysystem\Adapter\Local('files{DS}{$modelName}{DS}{$year}')
	],
]);
debug(Configure::check('attachment'));


try {
	Configure::load('attachment');
} catch (Exception $e) {

}

debug(Configure::read('Attachment'));
