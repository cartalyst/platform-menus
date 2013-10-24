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

	'create' => array(
		'legend'      => 'New Item',
		'description' => 'Add a new navigation item.',
	),

	'edit' => array(
		'legend'      => 'Edit Item',
		'description' => 'Update an existing navigation item.',
	),

	'name'      => 'Name',
	'name_help' => '..',

	'slug'      => 'Slug',
	'slug_help' => '..',

	'enabled'      => 'Status',
	'enabled_help' => '..',

	'parent'      => 'Parent',
	'parent_help' => '..',

	'type'      => 'Type',
	'type_help' => '..',

	'types' => array(
		'static' => 'Static',
		'page'   => 'Page',
	),

	'uri'      => 'Uri',
	'uri_help' => '..',

	'secure'      => 'HTTPS',
	'secure_help' => '..',

	'visibility'      => 'Visibility',
	'visibility_help' => '..',

	'visibilities' => array(
		'always'     => 'Show Always',
		'logged_in'  => 'Logged In',
		'logged_out' => 'Logged Out',
		'admin'      => 'Admin Only',
	),

	'attributes' => array(

		'id'      => 'Id',
		'id_help' => 'Id that will be assigned to the <li> element surrounding your menu item.',

		'class'      => 'Class',
		'class_help' => 'Class that will be assigned to the <li> element surrounding your menu item.',

		'name'      => 'Name',
		'name_help' => 'This name will be assigned to the <a> element surrounding your menu item.',

		'title' => 'Title',
		'title_help' => 'This title will be assined to the <a> element surrounding your menu item.',

		'target'      => 'Target',
		'target_help' => '.',

		'targets' => array(
			'self'   => 'Same Window',
			'blank'  => 'New Window',
			'parent' => 'Parent Frame',
			'top'    => 'Top Frame (Main Document)',
		),

	),

);
