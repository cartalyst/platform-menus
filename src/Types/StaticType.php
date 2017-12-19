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
 * @version    6.0.4
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
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
     * {@inheritDoc}
     */
    public function afterSave(Menu $child)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function beforeDelete(Menu $child)
    {
    }
}
