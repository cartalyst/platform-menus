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
	 * Display a listing of menus.
	 *
	 * @return mixed
	 */
	public function getIndex()
	{
		// Set the current active menu
		set_active_menu('admin-menus');

		// Show the page
		return View::make('platform/menus::index');
	}

	/**
	 * Datasource for the menus Data Grid.
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
			$count = $menu->getChildrenCount();

			$menus[] = array_merge($menu->toArray(), array(
				'children_count' => Lang::choice('platform/menus::table.children', $count, compact('count'))
			));
		}

		// Return the Data Grid object
		return DataGrid::make($menus, array(
			'id',
			'name',
			'slug',
			'children_count',
			'created_at',
		));
	}

	/**
	 * Show the form for creating a new menu.
	 *
	 * @return View
	 */
	public function getCreate()
	{
		return $this->showForm(null, 'create');
	}

	/**
	 * Handle posting of the form for creating a new menu.
	 *
	 * @return Redirect
	 */
	public function postCreate()
	{
		return $this->processForm();
	}

	/**
	 * Show the form for updating a menu.
	 *
	 * @param  mixed  $slug
	 * @return mixed
	 */
	public function getEdit($slug = null)
	{
		return $this->showForm($slug, 'update');
	}

	/**
	 * Handle posting of the form for updating a menu.
	 *
	 * @param  mixed  $slug
	 * @return Redirect
	 */
	public function postEdit($slug = null)
	{
		return $this->processForm($slug);
	}

	/**
	 * Remove the specified menu.
	 *
	 * @param  mixed  $slug
	 * @return Redirect
	 */
	public function getDelete($slug)
	{
		// Instantiate a new message bag
		$bag = new Bag;

		try
		{
			// Delete the menu
			API::delete("v1/menus/{$slug}");

			// Set the success message
			$bag->add('success', Lang::get('platform/menus::message.success.delete'));
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			$bag->add('error', Lang::get('platform/menus::message.error.delete'));
		}

		// Redirect to the menus management page
		return Redirect::toAdmin('menus')->with('notifications', $bag);
	}

	/**
	 * Shows the form.
	 *
	 * @param  mixed   $slug
	 * @param  string  $pageSegment
	 * @return mixed
	 */
	protected function showForm($slug = null, $pageSegment = null)
	{
		try
		{
			// Set the current active menu
			set_active_menu('admin-menus');

			// Fallback data
			$menu     = null;
			$children = null;

			// Do we have a menu identifier?
			if ( ! is_null($slug))
			{
				// Get the menu information
				$response = API::get("v1/menus/{$slug}");
				$menu     = $response['menu'];

				// Get this menu children
				$response = API::get("v1/menus/{$slug}/children");
				$children = $response['children'];
			}

			// Get all the menu slugs
			$response = API::get('v1/menus', array('flat' => true, 'attributes' => 'slug'));

			// Prepare the persisted slugs, so that we
			// don't end up with repeated slugs.
			$persistedSlugs = array_map(function($child)
			{
				return $child['slug'];
			}, $response['menus']);

			// Show the page
			return View::make('platform/menus::manage', compact('menu', 'children', 'persistedSlugs', 'pageSegment'));
		}
		catch (ApiHttpException $e)
		{
			// Set the error message
			$notifications = with(new Bag)->add('error', $e->getMessage());

			// Return to the menus management page
			return Redirect::toAdmin('menus')->with('notifications', $notifications);
		}
	}

	/**
	 * Processes the form.
	 *
	 * @param  mixed  $slug
	 * @return Redirect
	 */
	protected function processForm($slug = null)
	{
		// Get the tree
		$tree = Input::get('tree', array());

		// JSON string on non-AJAX form
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
			// Instantiate a new message bag
			$bag = new Bag;

			// Do we have a menu identifier?
			if (is_null($slug))
			{
				// Create the menu
				$response = API::post('v1/menus', compact('menu'));
				$slug     = $response['menu']->slug;

				// Set the success message
				$bag->add('success', Lang::get('platform/menus::message.success.create'));
			}

			// No, we are updating the menu
			else
			{
				// Update the menu
				$response = API::put("v1/menus/{$slug}", compact('menu'));
				$slug     = $response['menu']->slug;

				// Set the success message
				$bag->add('success', Lang::get('platform/menus::message.success.update'));
			}

			// Redirect to the menu edit page
			return Redirect::toAdmin("menus/edit/{$slug}")->with('notifications', $bag);
		}
		catch (ApiHttpException $e)
		{
			// Redirect to the appropriate page
			return Redirect::back()->withInput()->withErrors($e->getErrors());
		}
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
		// Existing menu children will be passing an ID to us. This
		// is advantageous to us, since a menu slug can be changed
		// without anything being messed up. For new items, we'll
		// use the slug that has been passed to us.
		$index = isset($child['id']) ? $child['id'] : $child['slug'];

		$new_child = array(
			'id'         => isset($child['id']) ? Input::get("children.{$index}.id") : null,
			'name'       => Input::get("children.{$index}.name"),
			'slug'       => Input::get("children.{$index}.slug"),
			'type'       => Input::get("children.{$index}.type", 'static'),
			'uri'        => Input::get("children.{$index}.uri"),
			'page_id'    => Input::get("children.{$index}.page_id"),
			'visibility' => Input::get("children.{$index}.visibility", 'always'),
			'secure'     => Input::get("children.{$index}.secure", 0),
			'target'     => Input::get("children.{$index}.target", 0),
			'class'      => Input::get("children.{$index}.class"),
			'enabled'    => Input::get("children.{$index}.enabled", 1),
		);

		// If we have children, call the function again
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
