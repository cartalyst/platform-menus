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
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

interface MenuRepositoryInterface {

	/**
	 * Return a dataset compatible with the data grid.
	 *
	 * @return mixed
	 */
	public function grid();

	/**
	 * Return all the menu entries.
	 *
	 * @return \Platform\Content\Content
	 */
	public function findAll();

	/**
	 * Get an menu by it's primary key.
	 *
	 * @param  int  $id
	 * @return \Platform\Content\Content
	 */
	public function find($id);

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
	 * @param  int    $id
	 * @param  array  $data
	 * @return \Illuminate\Support\MessageBag
	 */
	public function validForUpdate($id, array $data);

	/**
	 * Creates a menu with the given data.
	 *
	 * @param  array  $data
	 * @return \Cartalyst\Content\Content
	 */
	public function create(array $data);

	/**
	 * Updates a menu with the given data.
	 *
	 * @param  int    $id
	 * @param  array  $data
	 * @return \Cartalyst\Content\Content
	 */
	public function update($id, array $data);

	/**
	 * Deletes the given menu.
	 *
	 * @param  int  $id
	 * @return bool|null
	 */
	public function delete($id);

}
