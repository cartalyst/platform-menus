<?php namespace Platform\Menus\Controllers\Api\V1;
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

use Platform\Routing\Controllers\ApiController;
use Platform\Menus\Menu;

class MenusController extends ApiController {

	/**
	 * Holds the form validation rules.
	 *
	 * @var array
	 */
	protected $validationRules = array(
		'name' => 'required',
		'slug' => 'required|unique:menus,slug'
	);

	/**
	 * Holds the menu model.
	 *
	 * @var Platform\Menus\Model
	 */
	protected $model;

	/**
	 * Initializer.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$app = app();

		$this->model = $app->make('platform/menus::menu');
	}

	/**
	 * Display a listing of root menus.
	 *
	 * @return Cartalyst\Api\Http\Response
	 */
	public function index()
	{
		return $this->response(array('menus' => $this->model->allRoot()));
	}

	/**
	 * Create a new menu.
	 *
	 * @return Cartalyst\Api\Http\Response
	 */
	public function create()
	{

	}

	/**
	 * Returns information about the given menu.
	 *
	 * @param  string  $menuSlug
	 * @return Cartalyst\Api\Http\Response
	 */
	public function show($menuSlug)
	{
		// Get this menu information
		if ( ! $menu = $this->model->find($menuSlug))
		{
			return $this->response(\Lang::get('platform/menus:messages.does_not_exist', compact('menuSlug')), 404);
		}

		return $this->response(compact('menu'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @return Cartalyst\Api\Http\Response
	 */
	public function update($menuSlug)
	{
		// Get this menu information
		if ( ! $menu = $this->model->find($menuSlug))
		{
			return $this->response(\Lang::get('platform/menus:messages.does_not_exist', compact('menuSlug')), 404);
		}

		//
		foreach ($this->input('menu', array()) as $key => $value)
		{
			if ($key === 'children')
			{
				// \API::put('menus/'.$menuSlug.'/children', array('children' => $value));
			}
			else
			{
				$menu->{$key} = $value;
			}
		}

		// Update the menu
		if ($menu->save())
		{

		}

		return $this->response(compact('menu'));
	}

	/**
	 * Deletes the provided menu.
	 *
	 * @param  int  $menuSlug
	 * @return Cartalyst\Api\Http\Response
	 */
	public function destroy($menuSlug)
	{
		// Get this menu information
		if ( ! $menu = $this->model->find($menuSlug))
		{
			return $this->response(\Lang::get('platform/menus:messages.does_not_exist', compact('menuSlug')), 404);
		}



		die;

		// Was the menu deleted?
		if ($menu->delete())
		{
			return $this->response(\Lang::get('platform/menus::messages.delete.success'));
		}

		// Something went wrong
		return $this->response(\Lang::get('platform/menus::messages.delete.error'));
	}

}
