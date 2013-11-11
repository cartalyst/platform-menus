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
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use API;
use Cartalyst\Api\Http\ApiHttpException;
use HTML;
use View;

class Dropdown {

	/**
	 * Returns an HTML dropdown with all the root menus.
	 *
	 * @param  array   $attributes
	 * @param  array   $customOptions
	 * @return \View
	 */
	public function root($attributes = array(), $customOptions = array())
	{
		try
		{
			$response = API::get('v1/menus?root=true');

			$items = $response['menus'];

			foreach ($items as &$item)
			{
				$item->depth = $item->depth ?: 1;

				$item->children = array();
			}

			// Prepare the attributes
			$attributes = HTML::attributes($attributes);

			return View::make('platform/menus::widgets/dropdown', compact('items', 'attributes', 'customOptions'));
		}
		catch (ApiHttpException $e)
		{
			return;
		}
	}

	/**
	 * Returns an HTML dropdown with all the childrens of
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
		try
		{
			// Get the menu children
			$items = $this->getChildrenForSlug($slug, $depth);

			// Loop through and prepare the item for display
			foreach ($items as $item)
			{
				$this->prepareChildRecursively($item, $current);
			}

			// Prepare the attributes
			$attributes = HTML::attributes($attributes);

			return View::make('platform/menus::widgets/dropdown', compact('items', 'attributes', 'customOptions'));
		}
		catch (ApiHttpException $e)
		{
			return;
		}
	}

	/**
	 * Returns the children for a menu with the given slug.
	 *
	 * @param  string  $slug
	 * @param  int     $depth
	 * @return array
	 */
	protected function getChildrenForSlug($slug, $depth = 0)
	{
		$response = API::get("v1/menus/{$slug}", compact('depth'));

		return $response['children'];
	}

	/**
	 * Recursively prepares a child for presentation within the nav widget.
	 *
	 * @param  \Platform\Menus\Models\Menu  $item
	 * @param  string  $current
	 * @return void
	 */
	protected function prepareChildRecursively($item, $current = null)
	{
		// Get this item children
		$item->children = $item->getChildren();

		// Is this the current child?
		$item->isCurrent = $current == $item->id;

		// Recursive!
		foreach ($item->children as $grandChild)
		{
			$this->prepareChildRecursively($grandChild, $current);
		}
	}

}
