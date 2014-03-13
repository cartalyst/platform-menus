<?php namespace Platform\Menus\Repositories;
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
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

interface MenuRepositoryInterface {

	/**
	 * Returns a dataset compatible with data grid.
	 *
	 * @return \Platform\Menus\Models\Menu
	 */
	public function grid();

	/**
	 * Returns all the menu entries.
	 *
	 * @return \Platform\Menus\Models\Menu
	 */
	public function findAll();

	/**
	 * Returns all the root menus.
	 *
	 * @return \Platform\Menus\Models\Menu
	 */
	public function findAllRoot();

	/**
	 * Returns a menu by its primary key or slug.
	 *
	 * @param  int  $id
	 * @return \Platform\Menus\Models\Menu
	 */
	public function find($id);

	/**
	 * Returns a root menu by its primary key or slug.
	 *
	 * @param  int  $id
	 * @return \Platform\Menus\Models\Menu
	 */
	public function findRoot($id);

	/**
	 * Perform a basic search.
	 *
	 * @param  string  $column
	 * @param  mixed  $value
	 * @return \Platform\Menus\Models\Menu
	 */
	public function findWhere($column, $value);

	/**
	 * Return all the menu slugs.
	 *
	 * @return array
	 */
	public function slugs();

	/**
	 * Return all the available menu types.
	 *
	 * @return array
	 */
	public function getTypes();

	/**
	 * Determine if the given menu is valid for creation.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Support\MessageBag
	 */
	public function validForCreation(array $data);

	/**
	 * Determine if the given menu is valid for updating.
	 *
	 * @param  int  $id
	 * @param  array  $data
	 * @return \Illuminate\Support\MessageBag
	 */
	public function validForUpdate($id, array $data);

	/**
	 * Creates a menu with the given data.
	 *
	 * @param  array  $data
	 * @return \Platform\Menus\Models\Menu
	 */
	public function create(array $data);

	/**
	 * Updates a menu with the given data.
	 *
	 * @param  int  $id
	 * @param  array  $data
	 * @return \Platform\Menus\Models\Menu
	 */
	public function update($id, array $data);

	/**
	 * Deletes the given menu.
	 *
	 * @param  int  $id
	 * @return bool
	 */
	public function delete($id);

}
