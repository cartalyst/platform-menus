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
use Cartalyst\Support\Handlers\EventHandler as BaseEventHandler;

class EventHandler extends BaseEventHandler implements EventHandlerInterface {

	/**
	 * {@inheritDoc}
	 */
	public function subscribe(Dispatcher $dispatcher)
	{
		$dispatcher->listen('platform.menu.creating', __CLASS__.'@creating');
		$dispatcher->listen('platform.menu.created', __CLASS__.'@created');

		$dispatcher->listen('platform.menu.updating', __CLASS__.'@updating');
		$dispatcher->listen('platform.menu.updated', __CLASS__.'@updated');

		$dispatcher->listen('platform.menu.deleted', __CLASS__.'@deleted');
	}

	/**
	 * {@inheritDoc}
	 */
	public function creating(array $data)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function created(Menu $menu, array $data)
	{
		$this->flushCache($menu);
	}

	/**
	 * {@inheritDoc}
	 */
	public function updating(Menu $menu, array $data)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function updated(Menu $menu, array $data)
	{
		$this->flushCache($menu);
	}

	/**
	 * {@inheritDoc}
	 */
	public function deleted(Menu $menu)
	{
		$this->flushCache($menu);
	}

	/**
	 * Flush the cache.
	 *
	 * @param  \Platform\Menus\Models\Menu  $menu
	 * @return void
	 */
	protected function flushCache(Menu $menu)
	{
		$this->cache->forget('platform.menus.all');
		$this->cache->forget('platform.menus.all.root');

		$this->cache->forget('platform.menu.'.$menu->id);
		$this->cache->forget('platform.menu.'.$menu->slug);

		$this->cache->forget('platform.menu.root.'.$menu->id);
		$this->cache->forget('platform.menu.root.'.$menu->slug);
	}

}
