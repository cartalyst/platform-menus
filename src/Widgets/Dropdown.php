<?php namespace Platform\Menus\Widgets;
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
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use HTML;
use Platform\Menus\Repositories\MenuRepositoryInterface;
use View;

class Dropdown {

	/**
	 * Menus repository.
	 *
	 * @var \Platform\Menus\Repositories\MenuRepositoryInterface
	 */
	protected $menus;

	/**
	 * Constructor.
	 *
	 * @param  \Platform\Menus\Repositories\MenuRepositoryInterface
	 * @return void
	 */
	public function __construct(MenuRepositoryInterface $menus)
	{
		$this->menus = $menus;
	}

	/**
	 * Returns an HTML dropdown with all the root menus.
	 *
	 * @param  int    $current
	 * @param  array  $attributes
	 * @param  array  $customOptions
	 * @return \View
	 */
	public function root($current = null, $attributes = array(), $customOptions = array())
	{
		return $this->renderDropdown($this->menus->allRoot(), $current, $attributes, $customOptions);
	}

	/**
	 * Returns an HTML dropdown with all the children of
	 * the provided menu slug.
	 *
	 * @param  string  $slug
	 * @param  int     $depth
	 * @param  int     $current
	 * @param  array   $attributes
	 * @param  array   $customOptions
	 * @return \View
	 */
	public function show($slug, $depth, $current = null, $attributes = array(), $customOptions = array())
	{
		$menu = $this->menus->find($slug);

		return $this->renderDropdown($menu->findChildren($depth), $current, $attributes, $customOptions);
	}

	/**
	 * Render the view with the dropdown items.
	 *
	 * @param  array   $items
	 * @param  int     $current
	 * @param  array   $attributes
	 * @param  array   $customOptions
	 * @return \View
	 */
	protected function renderDropdown($items, $current, $attributes, $customOptions)
	{
		// Loop through and prepare the items for display
		foreach ($items as $item)
		{
			$this->prepareItemsRecursively($item, $current);
		}

		// Prepare the attributes
		$attributes = HTML::attributes($attributes);

		return View::make('platform/menus::widgets/dropdown', compact('items', 'attributes', 'customOptions'));
	}

	/**
	 * Recursively prepares the items for presentation.
	 *
	 * @param  \Platform\Menus\Models\Menu  $item
	 * @param  string  $current
	 * @return void
	 */
	protected function prepareItemsRecursively($item, $current = null)
	{
		// Get this item children
		$item->children = $item->getChildren();

		// Is this the current child?
		$item->isCurrent = $current == $item->id;

		// Make sure we have a proper item depth
		$item->depth = $item->depth ?: 1;

		// Recursive!
		foreach ($item->children as $children)
		{
			$this->prepareItemsRecursively($children, $current);
		}
	}

}
