<?php

return array(

	'already_exists' => 'Menu already exists!',
	#'does_not_exist' => 'Menu [:menuSlug] does not exist.',
	'does_not_exist' => 'Menu [:menuSlug] either not a root menu item or does not exist.',

	'create' => array(
		'error'   => 'Menu was not created, please try again.',
		'success' => 'Menu created successfully.'
	),

	'update' => array(
		'success'     => 'Menu :menuSlug was successfully updated.',
		'error'       => 'Menu was not updated, please try again.',
		'no_children' => 'No children hierarchy was provided.'
	),

	'delete' => array(
		'error'   => 'There was an issue deleting the menu. Please try again.',
		'success' => 'Menu was deleted successfully.'
	)

);
