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

class ChildrenController extends ApiController {

	/**
	 *
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
	 * Display the specified resource.
	 *
	 * @return Cartalyst\Api\Http\Response
	 */
	public function show($menuSlug)
	{
		if ( ! $menu = $this->model->find($menuSlug))
		{
			return $this->response("Could not find children for [$menuSlug] menu as it does not exist.", 404);
		}

		// Hydrate the children to the depth required
		$menu->hydrateChildren((int) \API::input('depth', 0));

		return $this->response(array('children' => $menu->getChildren()));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @return Cartalyst\Api\Http\Response
	 */
	public function update($menuSlug)
	{
		if ( ! $menu = $this->model->find($menuSlug))
		{
			return $this->response("Could not update children for [$menuSlug] menu as it does not exist.", 404);
		}

		$menu->mapChildren($this->input('children'));

		return $this->show($menuSlug);
	}

}
