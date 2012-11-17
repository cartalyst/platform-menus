<?php

return array(

	'name'        => 'Menus',
	'slug'        => 'platform/menus',
	'author'      => 'Cartalyst LLC',
	'description' => 'Manages all menus throughout the website admin.',
	'version'     => '2.0',
	'is_core'     => true,

	'autoload' => 'composer',

	'dependencies' => array(
		'platform/menus' => array(
			'composer' => 'platform/extension-menus',
		),
	),

	'routes' => function() {

		Route::any('test', function() {

			echo __FILE__;

		});
	}

);