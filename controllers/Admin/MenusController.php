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
	 * @return View
	 */
	public function getIndex()
	{
		// Set the current active menu
		set_active_menu('admin-menus');

		try
		{
			// Get all the menus
			$menus = \API::get('menus');
		}
		catch (ApiHttpException $e)
		{

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

	}
}
