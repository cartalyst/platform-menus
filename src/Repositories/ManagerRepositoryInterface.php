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
 * @version    4.0.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Menus\Repositories;

use Platform\Menus\Models\Menu;
use Platform\Menus\Types\TypeInterface;

interface ManagerRepositoryInterface
{
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
}
