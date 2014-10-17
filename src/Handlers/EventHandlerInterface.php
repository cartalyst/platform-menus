<?php namespace Platform\Menus\Handlers;
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
use Cartalyst\Support\Handlers\EventHandlerInterface as BaseEventHandlerInterface;

interface EventHandlerInterface extends BaseEventHandlerInterface {

	/**
	 * When a menu is being created.
	 *
	 * @return mixed
	 */
	public function creating();

	/**
	 * When a menu is created.
	 *
	 * @param  \Platform\Menus\Models\Menus  $menu
	 * @return mixed
	 */
	public function created(Menu $menu);

	/**
	 * When a menu is being updated.
	 *
	 * @param  \Platform\Menus\Models\Menus  $menu
	 * @return mixed
	 */
	public function updated(Menu $menu);

	/**
	 * When a menu is deleted.
	 *
	 * @param  \Platform\Menus\Models\Menus  $menu
	 * @return mixed
	 */
	public function deleted(Menu $menu);

}
