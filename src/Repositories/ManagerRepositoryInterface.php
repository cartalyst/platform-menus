<?php namespace Platform\Menus\Repositories;
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

use Platform\Menus\Models\Menu;
use Platform\Menus\Types\TypeInterface;

interface ManagerRepositoryInterface {

	/**
	 * Returns all the registered menu types.
	 *
	 * @return array
	 */
	public function getTypes();

	/**
	 * Registers an menu type.
	 *
	 * @param  \Platform\Menus\Types\TypeInterface  $type
	 * @return void
	 */
	public function registerType(TypeInterface $type);

	/**
	 * Returns the entity html form.
	 *
	 * @param \Platform\Menus\Models\Menu  $menu
	 * @param .. ?? ..  $entity
	 * @return string
	 */
	public function getEntityHtmlForm(Menu $menu, $entity);

}
