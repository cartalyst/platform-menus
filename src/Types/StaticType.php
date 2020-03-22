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

namespace Platform\Menus\Types;

use Platform\Menus\Models\Menu;

class StaticType extends AbstractType implements TypeInterface
{
    /**
     * Get the type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'static';
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave(Menu $child)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDelete(Menu $child)
    {
    }
}
