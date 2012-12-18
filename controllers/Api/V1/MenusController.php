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

use Platform\Foundation\Controllers\ApiController;
use Platform\Menus\Menu;

class MenusController extends ApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Cartalyst\Api\Http\Response
	 */
	public function index()
	{
		
	}

	/**
	 * Display the specified resource.
	 *
	 * @return Cartalyst\Api\Http\Response
	 */
	public function show($slug)
	{
		if ( ! $menu = Menu::find($slug))
		{
			return $this->response(array(), 404);
		}

		return $this->response(array('menu' => $menu));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @return Cartalyst\Api\Http\Response
	 */
	public function update($slug)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @return Cartalyst\Api\Http\Response
	 */
	public function destroy($slug)
	{
		//
	}

}