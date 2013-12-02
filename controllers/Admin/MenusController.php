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

use DataGrid;
use Illuminate\Support\MessageBag as Bag;
use Input;
use Lang;
use Platform\Admin\Controllers\Admin\AdminController;
use Platform\Menus\Repositories\MenuRepositoryInterface;
use Redirect;
use Sentry;
use View;

class MenusController extends AdminController {

	/**
	 * Menus repository.
	 *
	 * @var \Platform\Menus\Repositories\MenuRepositoryInterface
	 */
	protected $menus;

	/**
	 * Constructor.
	 *
	 * @param  \Platform\Menus\Repositories\MenuRepositoryInterface  $menus
	 * @return void
	 */
	public function __construct(MenuRepositoryInterface $menus)
	{
		parent::__construct();

		$this->menus = $menus;
	}

	/**
	 * Display a listing of menus.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		return View::make('platform/menus::index');
	}

	/**
	 * Datasource for the menus Data Grid.
	 *
	 * @return \Cartalyst\DataGrid\DataGrid
	 */
	public function grid()
	{
		return DataGrid::make($this->menus->grid(), array(
			'id',
			'name',
			'slug',
			'items_count',
			'created_at',
		));
	}

	/**
	 * Show the form for creating a new menu.
	 *
	 * @return \Illuminate\View\View
	 */
	public function create()
	{
		return $this->showForm('create');
	}

	/**
	 * Handle posting of the form for creating a new menu.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function store()
	{
		return $this->processForm('create');
	}

	/**
	 * Show the form for updating a menu.
	 *
	 * @param  mixed  $slug
	 * @return mixed
	 */
	public function edit($slug)
	{
		return $this->showForm('update', $slug);
	}

	/**
	 * Handle posting of the form for updating a menu.
	 *
	 * @param  mixed  $slug
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function update($slug)
	{
		return $this->processForm('update', $slug);
	}

	/**
	 * Remove the specified menu.
	 *
	 * @param  mixed  $slug
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function delete($slug)
	{
		// Delete the menu
		if ($this->menus->delete($slug))
		{
			return Redirect::toAdmin('menus')->withSuccess(Lang::get('platform/menus::message.success.delete'));
		}

		return Redirect::toAdmin('menus')->withErrors(Lang::get('platform/menus::message.error.delete'));
	}

	/**
	 * Shows the form.
	 *
	 * @param  string  $mode
	 * @param  mixed   $slug
	 * @return mixed
	 */
	protected function showForm($mode, $slug = null)
	{
		// Do we have a menu identifier?
		if ( ! is_null($slug))
		{
			// Get the menu information
			if ( ! $menu = $this->menus->find($slug))
			{
				return Redirect::toAdmin('menus')->withErrors(Lang::get('platform/menus::message.not_found', array('id' => $slug)));
			}

			// Get this menu children
			$children = $menu->findChildren(0);
		}

		// Get the persisted slugs
		$persistedSlugs = $this->menus->slugs();

		// Get a list of all the available groups
		$groups = Sentry::getGroupRepository()->createModel()->all();

		// Get all the registered menu types
		$types = $this->menus->getTypes();

		// Share some variables, because of views inheritance
		View::share(compact('groups', 'types'));

		// Show the page
		return View::make('platform/menus::manage', compact('mode', 'menu', 'children', 'persistedSlugs'));
	}

	/**
	 * Processes the form.
	 *
	 * @param  string  $mode
	 * @param  mixed   $slug
	 * @return \Illuminate\Http\RedirectResponse
	 */
	protected function processForm($mode, $slug = null)
	{
		// Get the tree
		$tree = json_decode(Input::get('menu-tree', array()), true);

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
		$input = array(
			'slug'     => Input::get('menu-slug'),
			'name'     => Input::get('menu-name'),
			'children' => $children,
		);

		// Do we have a menu identifier?
		if ($slug)
		{
			// Check if the input is valid
			$messages = $this->menus->validForUpdate($slug, $input);

			// Do we have any errors?
			if ($messages->isEmpty())
			{
				// Update the menu
				$menu = $this->menus->update($slug, $input);
			}
		}
		else
		{
			// Check if the input is valid
			$messages = $this->menus->validForCreation($input);

			// Do we have any errors?
			if ($messages->isEmpty())
			{
				// Create the menu
				$menu = $this->menus->create($input);
			}
		}

		// Do we have any errors?
		if ($messages->isEmpty())
		{
			// Prepare the success message
			$message = Lang::get("platform/menus::message.success.{$mode}");

			return Redirect::toAdmin("menus/{$menu->slug}/edit")->withSuccess($message);
		}

		return Redirect::back()->withInput()->withErrors($messages);
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
		$index = $child['id'];

		$new_child = array(
			'id'         => is_numeric($index) ? $index : null,
			'name'       => Input::get("children.{$index}.name"),
			'slug'       => Input::get("children.{$index}.slug"),
			'enabled'    => Input::get("children.{$index}.enabled", 1),
			'type'       => $type = Input::get("children.{$index}.type", 'static'),
			'secure'     => Input::get("children.{$index}.secure", 0),
			'visibility' => Input::get("children.{$index}.visibility", 'always'),
			//'groups'     => Input::get("children.{$index}.groups", array()),
			'class'  => Input::get("children.{$index}.class"),
			'target' => Input::get("children.{$index}.target"),
			'regex'  => Input::get("children.{$index}.regex"),
		);

		// Attach the type data
		$new_child['uri'] = Input::get("children.{$index}.static.uri");

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
