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
use Illuminate\Events\Dispatcher;
use Cartalyst\Support\Handlers\EventHandler;
use Platform\Menus\Repositories\MenuRepositoryInterface;

class MenuEventHandler extends EventHandler implements MenuEventHandlerInterface {

	/**
	 * {@inheritDoc}
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('platform.menu.created', 'Platform\Menus\Handlers\MenuEventHandler@onCreate');

		$dispatcher->listen('platform.menu.updated', 'Platform\Menus\Handlers\MenuEventHandler@onUpdate');

		$dispatcher->listen('platform.menu.deleted', 'Platform\Menus\Handlers\MenuEventHandler@onDelete');
	}

	/**
	 * {@inheritDoc}
	 */
	public function onCreate(Menu $menu)
	{
		$this->cache->forget('platform.menu.all');

		$this->cache->forget('platform.menu.all.root');

		$this->app['Platform\Menus\Repositories\MenuRepositoryInterface']->find($menu->id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function onUpdate(Menu $menu)
	{
		$this->cache->forget('platform.menu.all');

		$this->cache->forget('platform.menu.all.root');

		$this->cache->forget("platform.menu.{$menu->id}");

		$this->cache->forget("platform.menu.{$menu->slug}");

		$this->app['Platform\Menus\Repositories\MenuRepositoryInterface']->find($menu->id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function onDelete(Menu $menu)
	{
		$this->cache->forget('platform.menu.all');

		$this->cache->forget('platform.menu.all.root');

		$this->cache->forget("platform.menu.{$menu->id}");

		$this->cache->forget("platform.menu.{$menu->slug}");
	}

}
