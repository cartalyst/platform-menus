<?php

/**
 * Part of the Platform Menus extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Platform Menus extension
 * @version    3.2.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Menus\Repositories;

use Platform\Menus\Models\Menu;

interface MenuRepositoryInterface
{
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
     * Returns a menu by its slug.
     *
     * @param  string  $slug
     * @return \Platform\Menus\Models\Menu
     */
    public function findBySlug($slug);

    /**
     * Returns a root menu by its primary key or slug.
     *
     * @param  int  $id
     * @return \Platform\Menus\Models\Menu
     */
    public function findRoot($id);

    /**
     * Returns a root menu by its slug.
     *
     * @param  string  $slug
     * @return \Platform\Menus\Models\Menu
     */
    public function findRootBySlug($slug);

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
     * @param  array  $input
     * @return \Illuminate\Support\MessageBag
     */
    public function validForCreation(array $input);

    /**
     * Determine if the given menu is valid for updating.
     *
     * @param  \Platform\Menus\Models\Menu  $menu
     * @param  array  $input
     * @return \Illuminate\Support\MessageBag
     */
    public function validForUpdate(Menu $menu, array $input);

    /**
     * Creates a menu with the given data.
     *
     * @param  array  $input
     * @return bool|array
     */
    public function create(array $input);

    /**
     * Updates a menu with the given data.
     *
     * @param  int  $id
     * @param  array  $input
     * @return bool|array
     */
    public function update($id, array $input);

    /**
     * Creates or updates the given menu.
     *
     * @param  int  $id
     * @param  array  $input
     * @return bool|array
     */
    public function store($id, array $input);

    /**
     * Enables the given menu.
     *
     * @param  int  $id
     * @return bool|array
     */
    public function enable($id);

    /**
     * Disables the given menu.
     *
     * @param  int  $id
     * @return bool|array
     */
    public function disable($id);

    /**
     * Deletes the given menu.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete($id);
}
