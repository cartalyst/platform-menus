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
 * @version    9.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2020, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Menus\Repositories;

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
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * {@inheritdoc}
     */
    public function getType($type)
    {
        if (array_key_exists($type, $this->types)) {
            return $this->types[$type];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function registerType(TypeInterface $type)
    {
        $this->types[$type->getIdentifier()] = $type;
    }
}
