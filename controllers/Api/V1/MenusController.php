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

use API;
use Input;
use Lang;
use Platform\Routing\Controllers\ApiController;
use Response;

class MenusController extends ApiController {

	/**
	 * Holds the form validation rules.
	 *
	 * @var array
	 */
	protected $validationRules = array(
		'name' => 'required',
		'slug' => 'required|unique:menus,slug',
	);

	/**
	 * Holds the menu model.
	 *
	 * @var Platform\Menus\Menu
	 */
	protected $model;

	/**
	 * Initializer.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->model = app('Platform\Menus\Models\Menu');
	}

	/**
	 *
	 *
	 * @return Cartalyst\Api\Http\Response
	 */
	public function index()
	{
		// Get all the root menus
		if (Input::get('root'))
		{
			return Response::api(array('menus' => $this->model->allRoot()));
		}

		// Get all the menus on a flat array
		if (Input::get('flat'))
		{
			$menus = $this->model->findAll();

			if ($attributes = Input::get('attributes'))
			{
				$attributes = array_map('trim', explode(',', $attributes));

				$menus = array_map(function($menu) use ($attributes)
				{
					return array_intersect_key($menu->toArray(), array_flip($attributes));
				}, $menus);
			}
		}
		elseif ($extension = Input::get('extension'))
		{
			$menus = $this->model->newQuery()->where('extension', '=', $extension)->get();
		}
		else
		{
			$menus = $this->model->all();
		}

		return Response::api(compact('menus'));
	}

	/**
	 * Create a new menu.
	 *
	 * @return Cartalyst\Api\Http\Response
	 */
	public function create()
	{
		$menu = new $this->model(array(
			'name' => Input::get('menu.name'),
			'slug' => Input::get('menu.slug'),
		));
		$menu->makeRoot();

		if ($children = Input::get('menu.children'))
		{
			API::put("v1/menus/{$menu->getKey()}/children", compact('children'));
		}

		return Response::api(compact('menu'));
	}

	/**
	 * Returns information about the given menu.
	 *
	 * @param  string  $id
	 * @return Cartalyst\Api\Http\Response
	 */
	public function show($id)
	{
		// Get this menu information
		if ( ! $menu = $this->model->find($id))
		{
			return Response::api(Lang::get('platform/menus::message.does_not_exist', compact('id')), 404);
		}

		return Response::api(compact('menu'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @return Cartalyst\Api\Http\Response
	 */
	public function update($id)
	{
		// Get this menu information
		if ( ! $menu = $this->model->find($id))
		{
			return Response::api(Lang::get('platform/menus::message.does_not_exist', compact('id')), 404);
		}

		//
		foreach (Input::get('menu', array()) as $key => $value)
		{
			if ($key === 'children')
			{
				API::put("v1/menus/$id/children", array('children' => $value));
			}
			else
			{
				$menu->{$key} = $value;
			}
		}

		// Was the menu updated?
		if ($menu->save())
		{
			return Response::api(compact('menu'));
		}

		return Response::api(Lang::get('platform/menus::message.error.update'), 500);
	}

	/**
	 * Deletes the provided menu.
	 *
	 * @param  int  $id
	 * @return Cartalyst\Api\Http\Response
	 */
	public function destroy($id)
	{
		// Get this menu information
		if ( ! $menu = $this->model->find($id))
		{
			return Response::api(Lang::get('platform/menus::message.does_not_exist', compact('id')), 404);
		}

		// Delete the menu
		$menu->deleteWithChildren();

		return Response::api(Lang::get('platform/menus::message.delete.success'), 204);
	}

}
