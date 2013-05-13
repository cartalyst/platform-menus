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
use DataGrid;
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
	 * Datasource for the users Data Grid.
	 *
	 * @return Cartalyst\DataGrid\DataGrid
	 */
	public function getGrid()
	{
		// Get all the root menus
		$response = API::get('v1/menus', array('root' => true));
		$menus    = array();

		foreach ($response['menus'] as $menu)
		{
			$menus[] = array_merge($menu->toArray(), array(
				'children_count' => $menu->getChildrenCount(),
			));
		}

		return DataGrid::make($menus, array(
			'id',
			'name',
			'slug',
			'children_count',
			'created_at',
		));
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
			$response       = API::get('v1/menus', array('flat' => true, 'attributes' => 'slug'));
			$persistedSlugs = array_map(function($child)
			{
				return $child['slug'];
			}, $response['menus']);
		}
		catch (ApiHttpException $e)
		{
			$persistedSlugs = '';
		}

		$children = array();

		// Show the page
		return View::make('platform/menus::manage', compact('persistedSlugs', 'children'));
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
	 * Update a menu.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	public function getEdit($id = null)
	{
		// Set the current active menu
		set_active_menu('admin-menus');

		try
		{
			// Get the menu information
			$response = API::get("v1/menus/$id");
			$menu     = $response['menu'];

			// Get this menu children
			$response = API::get("v1/menus/$id/children");
			$children = $response['children'];

			// Get all the menu slugs
			$response       = API::get('v1/menus', array('flat' => true, 'attributes' => 'slug'));
			$persistedSlugs = array_map(function($child)
			{
				return $child['slug'];
			}, $response['menus']);
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
	 * Update a menu form processing page.
	 *
	 * @param  int  $id
	 * @return Redirect
	 */
	public function postEdit($id = null)
	{
		// Get the tree
		$tree = Input::get('tree');

		// JSON string on non-AJAX form.
		if (is_string($tree))
		{
			$tree = json_decode($tree, true);
		}

		// Prepare our children
		$children = array();

		foreach ($tree as $child)
		{
			// Ensure no bad data is coming through from POST
			if ( ! is_array($child))
			{
				continue;
			}

			$this->processChildRecursively($child, $children);
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
			if (is_null($id))
			{
				// Create the menu
				$response = API::post('v1/menus', compact('menu'));
				$id = $response['menu']->slug;

				// Prepare the success message
				$messages = with(new Bag)->add('success', Lang::get('platform/menus::message.success.create'));
			}

			// No, we are updating the menu
			else
			{
				// Update the menu
				API::put("v1/menus/$id", compact('menu'));

				// Prepare the success message
				$messages = with(new Bag)->add('success', Lang::get('platform/menus::message.success.update'));
			}

			// Redirect to the menu edit page
			return Redirect::toAdmin("menus/edit/$id")->with('messages', $messages);
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
	 * @param  int  $id
	 * @return Redirect
	 */
	public function getDelete($id)
	{
		try
		{
			// Delete the menu
			API::delete("v1/menus/$id");

			// Set the success message
			$messages = with(new Bag)->add('success', Lang::get('platform/menus::message.success.delete'));
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			$messages = with(new Bag)->add('error', Lang::get('platform/menus::message.error.delete'));
		}

		// Redirect to the menus management page
		return Redirect::toAdmin('menus')->with('messages', $messages);
	}

	/**
	 * Recursively processes a child node by extracting POST data
	 * from the admin UI so that we may structure a nice tree of
	 * pure data to send off to the API.
	 *
	 * @param  array  $child
	 * @param  array  $children
	 * @return void
	 */
	protected function processChildRecursively($child, &$children)
	{
		// Existing menu children will pass an ID through to us.
		// This is advantageous to use as the slug may change
		// without anything being messed up. For new items, we'll
		// resort to using the slug that has been passed through to
		// us.
		$index = isset($child['id']) ? $child['id'] : $child['slug'];

		$new_child = array(
			'id'                  => Input::get("children.$index.id"),
			'name'                => Input::get("children.$index.name"),
			'slug'                => Input::get("children.$index.slug"),
			'driver'              => 'static',
			'uri'                 => Input::get("children.$index.uri"),
			// 'page_id'          => Input::get('children.' . $child['id'] . '.page_id'),
			'class'               => Input::get("children.$index.class"),
			'target'              => Input::get("children.$index.target", 0),
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
				$this->processChildRecursively($child, $grand_children);
			}

			$new_child['children'] = $grand_children;
		}

		$children[] = $new_child;
	}

}
