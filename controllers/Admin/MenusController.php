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

use API;
use Cartalyst\Api\Http\ApiHttpException;
use Illuminate\Support\MessageBag as Bag;
use Input;
use Lang;
use Platform\Admin\Controllers\Admin\AdminController;
use Redirect;
use View;

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
			// Get all the root menus
			$response = API::get('v1/menus', array('root' => true));
			$menus    = $response['menus'];
		}
		catch (ApiHttpException $e)
		{
			// Redirect to the admin dashboard
			return Redirect::toAdmin('/');
		}

		// Show the page
		return View::make('platform/menus::index', compact('menus'));
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

		try
		{
			// Get all the menu slugs
			$response       = API::get('v1/menus', array('flat' => true, 'onlySlugs' => true));
			$persistedSlugs = json_encode($response['menus']);
		}
		catch (ApiHttpException $e)
		{
			$persistedSlugs = '';
		}

		$children = array();

		// Show the page
		return View::make('platform/menus::manage', compact('persistedSlugs'));
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
			$response = API::get("v1/menus/$menuSlug");
			$menu     = $response['menu'];

			// Get this menu children
			$response = API::get("v1/menus/$menuSlug/children");
			$children = $response['children'];

			// Get all the menu slugs
			$response       = API::get('v1/menus', array('flat' => true, 'attributes' => 'slug'));
			$persistedSlugs = $response['menus'];
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			$messages = with(new Bag)->add('error', $e->getMessage());

			// Return to the menus management page
			return Redirect::toAdmin('menus')->with('messages', $messages);
		}

		// Show the page
		return View::make('platform/menus::manage', compact('menu', 'children', 'persistedSlugs'));
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
		$children_hierarchy = Input::get('children_hierarchy');

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

		// Prepare the menu data for the API
		$menu = array(
			'slug'     => Input::get('menu-slug'),
			'name'     => Input::get('menu-name'),
			'children' => $children,
		);

		try
		{
			// Are we creating a menu?
			if (is_null($menuSlug))
			{
				// Create the menu
				$response = API::post('v1/menus', compact('menu'));
				$menuSlug = $response['menu']->slug;

				// Prepare the success message
				$messages = with(new Bag)->add('success', Lang::get('platform/menus::message.create.success'));
			}

			// No, we are updating the menu
			else
			{
				// Update the menu
				API::put("v1/menus/$menuSlug", compact('menu'));

				// Prepare the success message
				$messages = with(new Bag)->add('success', Lang::get('platform/menus::message.update.success'));
			}

			// Redirect to the menu edit page
			return Redirect::toAdmin("menus/edit/$menuSlug")->with('messages', $messages);
		}
		catch (ApiHttpException $e)
		{
			// Redirect to the appropriate page
			return Redirect::back()->withInput()->withErrors($e->getErrors());
		}
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
			// Delete the menu
			API::delete("v1/menus/$menuSlug");

			// Set the success message
			$messages = with(new Bag)->add('success', Lang::get('platform/menus::message.delete.success'));
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			$messages = with(new Bag)->add('error', Lang::get('platform/menus::message.delete.error'));
		}

		// Redirect to the menus management page
		return Redirect::toAdmin('menus')->with('messages', $messages);
	}





	protected function process_child_recursively($child, &$children)
	{
		$new_child = array(
			'name'                => Input::get("children.{$child['slug']}.name"),
			'slug'                => Input::get("children.{$child['slug']}.slug"),
			'driver'              => 'static',
			// 'uri'              => Input::get('children.' . $child['id'] . '.uri'),
			// 'page_id'          => Input::get('children.' . $child['id'] . '.page_id'),
			'class'               => Input::get("children.{$child['slug']}.class"),
			'target'              => Input::get("children.{$child['slug']}.target", 0),
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
