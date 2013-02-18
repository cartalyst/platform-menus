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

			# workaround to get all the menu slugs on a
			# flat array, this should get ALL the menus slugs
			# not only the direct children of this menu !!!
			$persistedSlugs = $this->flatMenuSlugs($children);
			/*
			try
			{
				// Get all the children.
				$all_children = API::get('menus/flat');
			}
			catch (APIClientException $e)
			{
				// Fallback array.
				$all_children = array();
			}

			// Get array of persisted menu slugs.
			// It's used by javascript to validate unique slugs on
			// client end in addition to server end.
			//
			$persisted_slugs = array();
			foreach ($all_children as $child)
			{
			    $persisted_slugs[] = array_get($child, 'slug');
			}
			sort($persisted_slugs); // Purely for debugging on JS end really.
			*/
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			# TODO !

			// Return to the menus management page
			return \Redirect::to(ADMIN_URI.'/menus')->with('error', $e->getMessage());
		}

		// Show the page
		return \View::make('platform/menus::manage', compact('menu', 'children', 'persistedSlugs'));
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



	protected function flatMenuSlugs($items, $return = array())
	{
		foreach ($items as $item)
		{
			$return[] = $item->slug;

			if ($children = $item->getChildren())
			{
				$return = $this->flatMenuSlugs($children, $return);
			}
		}

		return $return;
	}


}
