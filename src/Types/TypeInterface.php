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
 * @version    3.0.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Menus\Types;

use Platform\Menus\Models\Menu;

interface TypeInterface
{
    /**
     * Returns the type identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns a human friendly name for the type.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the name for the menu child.
     *
     * @param  \Platform\Menus\Menu  $child
     * @return string
     */
    public function getNameAttribute(Menu $child);

    /**
     * Returns the URL for the menu child.
     *
     * @param  \Platform\Menus\Models\Menu  $child
     * @param  array  $options
     * @return string
     */
    public function getUrlAttribute(Menu $child, array $options = []);

    /**
     * Return the form HTML template for a edit child of this type as well
     * as creating new children.
     *
     * @param  \Platform\Menus\Models\Menu  $child
     * @return \View
     */
    public function getFormHtml(Menu $child = null);

    /**
     * Return the HTML template used when creating a menu child of this type.
     *
     * @return \View
     */
    public function getTemplateHtml();

    /**
     * Event that is called after a menu children is saved.
     *
     * @param  \Platform\Menus\Models\Menu  $child
     * @return void
     */
    public function afterSave(Menu $child);

    /**
     * Event that is called before a children is deleted.
     *
     * @param  \Platform\Menus\Models\Menu  $child
     * @return void
     */
    public function beforeDelete(Menu $child);
}
