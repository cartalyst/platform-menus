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
	 * Returns HTML dropdown based off the provided menu slug.
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
			$children = $this->getChildrenForSlug($slug, $depth);

			// Loop through and prepare the child for display
			foreach ($children as $child)
			{
				$this->prepareChildRecursively($child, $current);
			}

			// Prepare the attributes
			$attributes = HTML::attributes($attributes);

			return View::make('platform/menus::widgets/dropdown', compact('children', 'attributes', 'customOptions'));
		}
		catch (ApiHttpException $e)
		{
			return '';
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
	 * @param  \Platform\Menus\Models\Menu  $child
	 * @return void
	 */
	protected function prepareChildRecursively($child, $current = null)
	{
		// Get this item children
		$child->children = $child->getChildren();

		// Is this the current child?
		$child->isCurrent = $current == $child->id;

		// Recursive!
		foreach ($child->children as $grandChild)
		{
			$this->prepareChildRecursively($grandChild, $current);
		}
	}

}
