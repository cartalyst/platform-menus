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
 * @version    4.0.2
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Menus\Handlers;

use Platform\Menus\Models\Menu;
use Cartalyst\Support\Handlers\EventHandlerInterface as BaseEventHandlerInterface;

interface EventHandlerInterface extends BaseEventHandlerInterface
{
    /**
     * When a menu is being created.
     *
     * @param  array  $data
     * @return mixed
     */
    public function creating(array $data);

    /**
     * When a menu is created.
     *
     * @param  \Platform\Menus\Models\Menu  $menu
     * @return mixed
     */
    public function created(Menu $menu);

    /**
     * When a menu is being updated.
     *
     * @param  \Platform\Menus\Models\Menu  $menu
     * @param  array  $data
     * @return mixed
     */
    public function updating(Menu $menu, array $data);

    /**
     * When a menu is updated.
     *
     * @param  \Platform\Menus\Models\Menu  $menu
     * @return mixed
     */
    public function updated(Menu $menu);

    /**
     * When a menu is deleted.
     *
     * @param  \Platform\Menus\Models\Menu  $menu
     * @return mixed
     */
    public function deleted(Menu $menu);
}
