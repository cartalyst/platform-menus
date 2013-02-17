<?php namespace Platform\Menus\Controllers\Admin;
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

use Cartalyst\Api\Http\ApiHttpException;
use Platform\Admin\Controllers\Admin\AdminController;

class MenusController extends AdminController {

	/**
	 * Menu management main page.
	 *
	 * @return mixed
	 */
	public function getIndex()
	{
		// Set the current active menu
		set_active_menu('admin-menus');

		try
		{
			// Get all the menus
			$result = \API::get('menus');
			$menus = $result['menus'];
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			# TODO !

			// Redirect to the admin dashboard
			return \Redirect::to(ADMIN_URI);
		}

		// Show the page
		return \View::make('platform/menus::index', compact('menus'));
	}

	/**
	 * Create a new menu.
	 *
	 * @return View
	 */
	public function getCreate()
	{
		// Set the current active menu
		set_active_menu('admin-menus');

		// Show the page
		return \View::make('platform/menus::manage');
	}

	/**
	 * Create a new menu form processing page.
	 *
	 * @return Redirect
	 */
	public function postCreate()
	{
		return $this->postEdit();
	}

	/**
	 * Update a menu
	 *
	 * @param  string  $slug
	 * @return View
	 */
	public function getEdit($slug = null)
	{
		// Set the current active menu
		set_active_menu('admin-menus');

		try
		{
			// Get the menu information
			$result = \API::get('menus/'.$slug);
			$menu   = $result['menu'];

			// Get this menu children
			$result   = \API::get('menus/'.$slug.'/children');
			$children = $result['children'];
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			# TODO !

			// Return to the menus management page
			return \Redirect::to(ADMIN_URI.'/menus')->with('error', $e->getMessage());
		}

		// Show the page
		return \View::make('platform/menus::manage', compact('menu', 'children'));
	}

	/**
	 * Menu update form processing page.
	 *
	 * @param  string  $slug
	 * @return Redirect
	 */
	public function postEdit($slug = null)
	{

	}

	/**
	 * Delete a menu.
	 *
	 * @param  string  $slug
	 * @return Redirect
	 */
	public function getDelete($slug)
	{
		try
		{
			\API::delete('menus/'.$slug);

			// Set the success message
			# TODO !
		}
		catch (ApiHttpException $e)
		{
			// Set the error message.
			# TODO !
		}

		// Redirect to the menus management page
		return \Redirect::to(ADMIN_URI.'/menus');
	}

}
