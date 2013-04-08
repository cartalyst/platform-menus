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
use Platform\Ui\Menu;

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
	 * @var Platform\Ui\Model
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
	 *
	 *
	 * @return Cartalyst\Api\Http\Response
	 */
	public function index()
	{
		// Get all the root menus
		if (\Input::get('root'))
		{
			$menus = $this->model->allRoot();
		}

		// Get all the menus on a flat array
		elseif (\Input::get('flat'))
		{
			if (\Input::get('onlySlugs'))
			{
				$menus = array();

				foreach ($this->model->findAll() as $menu)
				{
					$menus[] = $menu->slug;
				}
			}
			else
			{
				$menus = $this->model->findAll();
			}
		}
		else
		{
			$menus = $this->model->all(); # same as the above
		}

		return \Response::api(compact('menus'));
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
			return \Response::api(\Lang::get('platform/menus:messages.does_not_exist', compact('menuSlug')), 404);
		}

		return \Response::api(compact('menu'));
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
			return \Response::api(\Lang::get('platform/menus:messages.does_not_exist', compact('menuSlug')), 404);
		}

		//
		foreach (\Input::get('menu', array()) as $key => $value)
		{
			if ($key === 'children')
			{
				\API::put('menus/'.$menuSlug.'/children', array('children' => $value));
			}
			else
			{
				$menu->{$key} = $value;
			}
		}

		// Was the menu updated?
		if ($menu->save())
		{
			return \Response::api(compact('menu'));
		}

		# nopp
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
			return \Response::api(\Lang::get('platform/menus:messages.does_not_exist', compact('menuSlug')), 404);
		}

		// Was the menu deleted?
		if ($menu->delete())
		{
			return \Response::api(\Lang::get('platform/menus::messages.delete.success'));
		}

		// Something went wrong
		return \Response::api(\Lang::get('platform/menus::messages.delete.error'));
	}

}
