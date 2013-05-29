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

use Input;
use Platform\Routing\Controllers\ApiController;
use Response;

class ChildrenController extends ApiController {

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
	 * Display the specified resource.
	 *
	 * @return Cartalyst\Api\Http\Response
	 */
	public function show($id)
	{
		if ( ! $menu = $this->model->find($id))
		{
			return Response::api("Could not find children for [$id] menu as it does not exist.", 404);
		}

		$depth = (int) Input::get('depth', 0);

		if (Input::get('enabled'))
		{
			$children = $menu->findEnabledChildren($depth);
		}
		else
		{
			// Hydrate the children to the depth required
			$children = $menu->findChildren($depth);
		}

		return Response::api(compact('children'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @return Cartalyst\Api\Http\Response
	 */
	public function update($id)
	{
		if ( ! $menu = $this->model->find($id))
		{
			return Response::api("Could not update children for [$id] menu as it does not exist.", 404);
		}

		$menu->mapTree(Input::get('children'));

		return $this->show($id);
	}

}
