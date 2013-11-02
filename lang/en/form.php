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
	'name_help' => 'Type your menu item name.',

	'slug'      => 'Slug',
	'slug_help' => 'Single word, no spaces and no special words. Dashes are allowed.',

	'enabled'      => 'Status',
	'enabled_help' => 'What is this menu item status?',

	'parent'      => 'Parent',
	'parent_help' => 'Chose the parent that this item belongs to or leave the default option selected for it to be a "root" menu item of this menu.',

	'type'      => 'Type',
	'type_help' => 'Select the item url type.',

	'types' => array(
		'static' => 'Static',
		'page'   => 'Page', # this will be removed later
	),

	'uri'      => 'Uri',
	'uri_help' => 'Type in your menu item uri.',

	'secure'      => 'HTTPS',
	'secure_help' => 'Should',

	'visibility'      => 'Visibility',
	'visibility_help' => 'When this menu item should be seen?',

	'visibilities' => array(
		'always'     => 'Show Always',
		'logged_in'  => 'Logged In',
		'logged_out' => 'Logged Out',
		'admin'      => 'Admin Only',
	),

	'groups'      => 'Groups',
	'groups_help' => 'What user groups should be able to see this menu item?',

	'class'      => 'Class',
	'class_help' => 'Class that will be assigned to the <li> element surrounding your menu item.',

	'target'      => 'Target',
	'target_help' => 'The target attribute specifies where to open the menu item.',

	'targets' => array(
		'self'   => 'Same Window',
		'blank'  => 'New Window',
		'parent' => 'Parent Frame',
		'top'    => 'Top Frame (Main Document)',
	),

	'regex'      => 'Regular Expression',
	'regex_help' => 'Regex pattern for advanced "selected" states.'

);
