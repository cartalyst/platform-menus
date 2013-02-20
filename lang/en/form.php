<?php
/**
 * Part of the Platform application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Platform
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

return array(

	'legend' => 'Menu Properties',

	'root' => array(

		'name' => 'Name',
		'slug' => 'Slug',

	),

	// Common
	'child' => array(

		'name'   => 'Name',
		'slug'   => 'Slug',
		'type'   => array(
			'title'  => 'Type',
			'static' => 'Static',
			'page'   => 'Page',
		),

		'uri'    => 'Uri',
		'secure' => 'Make Secure (HTTPS)',
		'class'  => 'CSS class',

		'target' => array(
			'title'  => 'Target',
			'self'   => 'Same Window',
			'blank'  => 'New Window',
			'parent' => 'Parent Frame',
			'top'    => 'Top Frame (Main Document)',
		),

		'visibility' => array(
			'title'      => 'Visibility',
			'always'     => 'Show Always',
			'logged_in'  => 'Logged In',
			'logged_out' => 'Logged Out',
			'admin'      => 'Admin Only',
		),

		'groups' => array(
			'title' => 'Group Visibility',
		),

	),

	// Create specific
	'create' => array(
		'child' => array(
			'legend' => 'New Child',
		),
	),

	// Update specific
	'update' => array(
		'child' => array(
			'legend' => 'Edit Child',
		),
	),

);
