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

// Register a function to set the active menu
if ( ! function_exists('set_active_menu'))
{
	/**
	 * Sets the active menu for the current request.
	 *
	 * @param  string  $slug
	 * @return void
	 */
	function set_active_menu($slug)
	{
		$app = app();
		$app['platform.menus.active'] = $slug;
	}
}

// Register a function to retrieve the active menu
if ( ! function_exists('get_active_menu'))
{
	/**
	 * Gets the active menu for the current request.
	 *
	 * @return string  $slug
	 */
	function get_active_menu()
	{
		$app = app();

		// We're only interested on providing a default
		// menu for the admin area only.
		if ( ! isset($app['platform.menus.active']) and is_admin())
		{
			$app['platform.menus.active'] = 'admin';
		}

		return $app['platform.menus.active'];
	}
}
