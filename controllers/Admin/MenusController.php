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
	 * @param  string  $menuSlug
	 * @return mixed
	 */

	public function getEdit($menuSlug = null)
	{
		// Set the current active menu
		set_active_menu('admin-menus');

		try
		{
			// Get the menu information
			$result = \API::get('menus/'.$menuSlug);
			$menu   = $result['menu'];

			// Get this menu children
			$result   = \API::get('menus/'.$menuSlug.'/children');
			$children = $result['children'];

			# workaround to get all the menu slugs on a
			# flat array, this should get ALL the menus slugs
			# not only the direct children of this menu !!!
			$persistedSlugs = $this->flatMenuSlugs($children);
			/*
			try
			{
				// Get all the children.
				$all_children = API::get('menus', array('flat' => true, 'onlySlugs' => true));
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
	 * @param  string  $menuSlug
	 * @return Redirect
	 */
	public function postEdit($menuSlug = null)
	{
		// Get the children hierarchy.
		$children_hierarchy = \Input::get('children_hierarchy');

		// JSON string on non-AJAX form.
		if (is_string($children_hierarchy))
		{
			$children_hierarchy = json_decode($children_hierarchy, true);
		}

		// Prepare our children
		$children = array();

		foreach ($children_hierarchy as $child)
		{
			// Ensure no bad data is coming through from POST
			if ( ! is_array($child))
			{
				continue;
			}

			$this->process_child_recursively($child, $children);
		}

		// Prepare data for the API
		$data = array();

		// Declare all the inputs we need to check
		$inputs = array(
			'name' => 'menu-name',
			//'slug' => 'menu-slug'
		);

		//
		foreach ($inputs as $input => $slug)
		{
			if ($$input = \Input::get($slug))
			{
				$data[$input] = $$input;
			}
		}

		// Do we have children?
		if (count($children) > 0)
		{
			$data['children'] = $children;
		}

		try
		{
			// Make the request
			\API::put('menus/'.$menuSlug, array('menu' => $data));

			// Set the success message
			# TODO !
		}
		catch (APIClientException $e)
		{
			// Set the error message
			# TODO !
		}

		//
		return \Redirect::to(ADMIN_URI.'/menus/edit/'.$menuSlug);
	}

	/**
	 * Delete a menu.
	 *
	 * @param  string  $menuSlug
	 * @return Redirect
	 */
	public function getDelete($menuSlug)
	{
		try
		{
			\API::delete('menus/'.$menuSlug);

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


	protected function process_child_recursively($child, &$children)
	{
		$new_child = array(
			'name'                => \Input::get('children.' . $child['slug'] . '.name'),
			'slug'                => \Input::get('children.' . $child['slug'] . '.slug'),
			// 'uri'              => Input::get('children.' . $child['id'] . '.uri'),
			// 'page_id'          => Input::get('children.' . $child['id'] . '.page_id'),
			// 'class'            => Input::get('children.' . $child['id'] . '.class'),
			// 'target'           => Input::get('children.' . $child['id'] . '.target', Menu::TARGET_SELF),
			// 'visibility'       => Input::get('children.' . $child['id'] . '.visibility', Menu::VISIBILITY_ALWAYS),
			// 'group_visibility' => (array) Input::get('children.' . $child['id'] . '.group_visibility'),
			// 'status'           => Input::get('children.' . $child['id'] . '.status', 1),
			// 'type'             => Input::get('children.' . $child['id'] . '.type', Menu::TYPE_STATIC),
		);

		// Determine if we're a new child or not. If we're
		// new, we don't attach an ID. Nesty will handle the
		// rest.
		/*
		if ( ! Input::get('children.' . $child['id'] . '.is_new'))
		{
			$new_child['id'] = $child['id'];
		}

		// Now, look for secure URLs
		if ($new_child['type'] == Menu::TYPE_STATIC and URL::valid($new_child['uri']))
		{
			$new_child['secure'] = (int) starts_with($new_child['uri'], 'https://');
		}

		// Relative URL, look in the POST data
		else
		{
			$new_child['secure'] = \Input::get('children.' . $child['id'] . '.secure', 0);
		}
		*/

		// If we have children, call the function again.
		//
		if ( ! empty($child['children']) and is_array($child['children']) and count($child['children']) > 0)
		{
			$grand_children = array();

			foreach ($child['children'] as $child)
			{
				$this->process_child_recursively($child, $grand_children);
			}

			$new_child['children'] = $grand_children;
		}

		$children[] = $new_child;
	}

}
