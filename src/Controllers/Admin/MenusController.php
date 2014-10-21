<?php namespace Platform\Menus\Controllers\Admin;
/**
 * Part of the Platform Menus extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Platform Menus extension
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Platform\Access\Controllers\AdminController;
use Platform\Menus\Repositories\MenuRepositoryInterface;

class MenusController extends AdminController {

	/**
	 * Menus repository.
	 *
	 * @var \Platform\Menus\Repositories\MenuRepositoryInterface
	 */
	protected $menus;

	/**
	 * {@inheritDoc}
	 */
	protected $csrfWhitelist = [
		'executeAction',
	];

	/**
	 * Holds all the mass actions we can execute.
	 *
	 * @var array
	 */
	protected $actions = [
		'delete',
	];

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
		return view('platform/menus::index');
	}

	/**
	 * Datasource for the menus Data Grid.
	 *
	 * @return \Cartalyst\DataGrid\DataGrid
	 */
	public function grid()
	{
		$columns = [
			'id',
			'name',
			'slug',
			'items_count',
			'created_at',
		];

		$settings = [
			'sort'      => 'created_at',
			'direction' => 'desc',
		];

		return datagrid($this->menus->grid(), $columns, $settings);
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
		if ($this->menus->delete($slug))
		{
			return redirect()->toAdmin('menus')->withSuccess(
				trans('platform/menus::message.success.delete')
			);
		}

		return redirect()->toAdmin('menus')->withErrors(
			trans('platform/menus::message.error.delete')
		);
	}

	/**
	 * Executes the mass action.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function executeAction()
	{
		$action = input('action');

		if (in_array($action, $this->actions))
		{
			foreach (input('entries', []) as $entry)
			{
				$this->menus->{$action}($entry);
			}

			return response('Success');
		}

		return response('Failed', 500);
	}

	/**
	 * Shows the form.
	 *
	 * @param  string  $mode
	 * @param  mixed  $id
	 * @return mixed
	 */
	protected function showForm($mode, $id = null)
	{
		if ( ! $data = $this->menus->getPreparedMenu($id))
		{
			return redirect()->toAdmin('menus')->withErrors(
				trans('platform/menus::message.not_found', compact('id'))
			);
		}

		extract($data);

		// Share some variables, because of views inheritance
		view()->share(compact('roles', 'types'));

		return view('platform/menus::manage', compact(
			'menu', 'children', 'persistedSlugs', 'mode'
		));
	}

	/**
	 * Processes the form.
	 *
	 * @param  string  $mode
	 * @param  mixed  $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	protected function processForm($mode, $id = null)
	{
		// Store the menu
		list($messages, $menu) = $this->menus->store($id, request()->all());

		// Do we have any errors?
		if ($messages->isEmpty())
		{
			return redirect()->toAdmin("menus/{$menu->id}")->withSuccess(
				trans("platform/menus::message.success.{$mode}")
			);
		}

		// Redirect to the previous page
		return redirect()->back()->withInput()->withErrors($messages);
	}

}
