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
use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;
use Platform\Menus\Models\Menu;
use Platform\Menus\Repositories\MenuRepositoryInterface;

class MenuEventHandler implements MenuEventHandlerInterface {

	/**
	 * The container instance.
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $app;

	/**
	 * The menu repository.
	 *
	 * @var \Platform\Menus\Repositories\MenuRepositoryInterface
	 */
	protected $menu;

 	/**
	 * The Cache manager instance.
	 *
	 * @var \Illuminate\Cache\CacheManager
	 */
	protected $cache;

	/**
	 * Constructor.
	 *
	 * @param  \Platform\Menus\Repositories\MenuRepositoryInterface  $menu
	 * @param  \Illuminate\Container\Container  $app
	 * @param  \Illuminate\Cache\CacheManager  $cache
	 * @return void
	 */
	public function __construct(MenuRepositoryInterface $menu, Container $app, CacheManager $cache)
	{
		$this->menu = $menu;

		$this->app = $app;

		$this->cache = $cache;
	}

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

		$this->menu->find($menu->id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function onUpdate(Menu $menu)
	{
		$this->cache->forget('platform.menu.all');

		$this->cache->forget("platform.menu.{$menu->id}");
		$this->cache->forget("platform.menu.{$menu->slug}");

		$this->menu->find($menu->id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function onDelete(Menu $menu)
	{
		$this->cache->forget('platform.menu.all');

		$this->cache->forget("platform.menu.{$menu->id}");
		$this->cache->forget("platform.menu.{$menu->slug}");
	}

}
