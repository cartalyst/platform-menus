<?php

/*
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
 * @version    10.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2020, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Menus\Handlers;

use Platform\Menus\Models\Menu;
use Illuminate\Contracts\Events\Dispatcher;
use Cartalyst\Support\Handlers\EventHandler as BaseEventHandler;

class EventHandler extends BaseEventHandler implements EventHandlerInterface
{
    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function creating(array $data)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function created(Menu $menu)
    {
        $this->flushCache($menu);
    }

    /**
     * {@inheritdoc}
     */
    public function updating(Menu $menu, array $data)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function updated(Menu $menu)
    {
        $this->flushCache($menu);
    }

    /**
     * {@inheritdoc}
     */
    public function deleted(Menu $menu)
    {
        $this->flushCache($menu);
    }

    /**
     * Flush the cache.
     *
     * @param \Platform\Menus\Models\Menu $menu
     *
     * @return void
     */
    protected function flushCache(Menu $menu)
    {
        $this->cache->forget('platform.menus.all');
        $this->cache->forget('platform.menus.all.root');

        $this->cache->forget('platform.menu.'.$menu->id);
        $this->cache->forget('platform.menu.slug.'.$menu->slug);

        $this->cache->forget('platform.menu.root.'.$menu->id);
        $this->cache->forget('platform.menu.root.slug.'.$menu->slug);
    }
}
