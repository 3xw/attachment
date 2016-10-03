<?php
use Cake\Core\Configure;

Configure::load('Attachment.attachment');
collection((array)Configure::read('Attachment.config'))->each(function ($file) {
    Configure::load($file);
});

//debug(Configure::read('Attachment'));
