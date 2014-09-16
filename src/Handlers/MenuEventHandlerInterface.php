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

use Illuminate\Events\Dispatcher;
use Platform\Menus\Models\Menu;

interface MenuEventHandlerInterface {

	/**
	 * Registers the event listeners using the given dispatcher instance.
	 *
	 * @param  \Illuminate\Events\Dispatcher  $dispatcher
	 * @return void
	 */
	public function subscribe(Dispatcher $dispatcher);

	/**
	 * Triggered when a menu is created.
	 *
	 * @param  \Platform\Menus\Models\Menus  $menus
	 * @return void
	 */
	public function onCreate(Menu $menu);

	/**
	 * Triggered when a menu is updated.
	 *
	 * @param  \Platform\Menus\Models\Menus  $menus
	 * @return void
	 */
	public function onUpdate(Menu $menu);

	/**
	 * Triggered when a menu is deleted.
	 *
	 * @param  \Platform\Menus\Models\Menus  $menus
	 * @return void
	 */
	public function onDelete(Menu $menu);

}
