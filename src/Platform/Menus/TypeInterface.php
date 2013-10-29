<?php namespace Platform\Menus;
/**
 * Part of the Platform application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Platform
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Platform\Menus\Models\Menu;

interface TypeInterface {

	/**
	 * Get the type identifier.
	 *
	 * @return string
	 */
	public function getIdentifier();

	/**
	 * Get a human friendly name for the type.
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Get the name for the menu child.
	 *
	 * @param  Platform\Menus\Models\Menu  $child
	 * @return string
	 */
	public function getChildName(Menu $child);

	/**
	 * Get the URL for the menu child.
	 *
	 * @param  Platform\Menus\Models\Menu  $child
	 * @param  array  $options
	 * @return string
	 */
	public function getChildUrl(Menu $child, array $options = array());

	/**
	 * Called after a menu child is saved. Attach any links
	 * and relationships.
	 *
	 * @param  Platform\Menus\Models\Menu  $child
	 * @return void
	 */
	public function afterSave(Menu $child);

	/**
	 * Called before a child is deleted. Detach any links
	 * and relationships.
	 *
	 * @param  Platform\Menus\Models\Menu  $child
	 * @return void
	 */
	public function beforeDelete(Menu $child);

	/**
	 * Return the HTML template used when creating a menu child of this type.
	 *
	 * @return string|Illuminate\Support\Contracts\RenderableInterface
	 */
	public function getTemplateHtml();

	/**
	 * Return the form HTML template for a new child of this type.
	 *
	 * @return string|Illuminate\Support\Contracts\RenderableInterface
	 */
	public function getTemplateFormHtml();

	/**
	 * Return the HTML template used when editing a menu child of this type.
	 *
	 * @param  Platform\Menus\Models\Menu  $child
	 * @return string|Illuminate\Support\Contracts\RenderableInterface
	 */
	public function getEditHtml(Menu $child);

	/**
	 * Return the form HTML template for a edit child of this type as well
	 * as creating new children.
	 *
	 * @param  Platform\Menus\Models\Menu  $child
	 * @return string|Illuminate\Support\Contracts\RenderableInterface
	 */
	public function getFormHtml(Menu $child = null);

}
