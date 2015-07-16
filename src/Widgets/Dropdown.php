<?php namespace Platform\Menus\Widgets;
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
 * @version    1.2.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Platform\Menus\Models\Menu;
use Illuminate\Html\HtmlBuilder;
use Platform\Menus\Repositories\MenuRepositoryInterface;

class Dropdown {

	/**
	 * The Menus repository.
	 *
	 * @var \Platform\Menus\Repositories\MenuRepositoryInterface
	 */
	protected $menus;

	/**
	 * The html builder instance.
	 *
	 * @var \Illuminate\Html\HtmlBuilder
	 */
	protected $html;

	/**
	 * Constructor.
	 *
	 * @param  \Platform\Menus\Repositories\MenuRepositoryInterface  $menus
	 * @return void
	 */
	public function __construct(MenuRepositoryInterface $menus, HtmlBuilder $html)
	{
		$this->menus = $menus;

		$this->html = $html;
	}

	/**
	 * Returns an HTML dropdown with all the root menus.
	 *
	 * @param  int  $current
	 * @param  array  $attributes
	 * @param  array  $options
	 * @return \Illuminate\View\View
	 */
	public function root($current = null, array $attributes = [], array $options = [])
	{
		return $this->renderDropdown($this->menus->allRoot(), $current, $attributes, $options);
	}

	/**
	 * Returns an HTML dropdown with all the children of
	 * the provided menu slug.
	 *
	 * @param  string  $slug
	 * @param  int  $depth
	 * @param  int  $current
	 * @param  array  $attributes
	 * @param  array  $options
	 * @return \Illuminate\View\View
	 */
	public function show($slug, $depth, $current = null, array $attributes = [], array $options = [])
	{
		$menu = $this->menus->find($slug);

		return $this->renderDropdown($menu->findChildren($depth), $current, $attributes, $options);
	}

	/**
	 * Render the view with the dropdown items.
	 *
	 * @param  array  $items
	 * @param  int  $current
	 * @param  array  $attributes
	 * @param  array  $options
	 * @return \Illuminate\View\View
	 */
	protected function renderDropdown(array $items, $current, array $attributes, array $options)
	{
		// Loop through and prepare the items for display
		foreach ($items as $item)
		{
			$this->prepareItemsRecursively($item, $current);
		}

		// Prepare the attributes
		$attributes = $this->html->attributes($attributes);

		return view('platform/menus::widgets/dropdown', compact('items', 'attributes', 'options'));
	}

	/**
	 * Recursively prepares the items for presentation.
	 *
	 * @param  \Platform\Menus\Models\Menu  $item
	 * @param  string  $current
	 * @return void
	 */
	protected function prepareItemsRecursively(Menu $item, $current = null)
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
