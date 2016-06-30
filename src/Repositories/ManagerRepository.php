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
 * @version    3.0.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Menus\Repositories;

use Platform\Menus\Models\Menu;
use Platform\Menus\Types\TypeInterface;

class ManagerRepository implements ManagerRepositoryInterface
{
    /**
     * Array of registered attribute types.
     *
     * @var array
     */
    protected $types = [];

    /**
     * {@inheritDoc}
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * {@inheritDoc}
     */
    public function getType($type)
    {
        if (array_key_exists($type, $this->types)) {
            return $this->types[$type];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function registerType(TypeInterface $type)
    {
        $this->types[$type->getIdentifier()] = $type;
    }
}
