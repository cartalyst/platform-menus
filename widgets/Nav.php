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
use Cartalyst\Sentry\Facades\Laravel\Sentry;
use InvalidArgumentException;
use URL;
use View;

class Nav {

	/**
	 * Holds the current request path information.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->path = URL::current();
	}

	/**
	 * Returns navigation HTML based off the current active menu.
	 *
	 * @param  string  $slug
	 * @param  string  $depth
	 * @param  string  $cssClass
	 * @param  string  $beforeUri
	 * @return \View
	 */
	public function show($slug, $depth = 0, $cssClass = null, $beforeUri = null)
	{
		try
		{
			// Get the menu children
			$children = $this->getChildrenForSlug($slug, $depth);

			// Loop through and prepare the child for display
			foreach ($children as $child)
			{
				$this->prepareChildRecursively($child, $beforeUri);
			}

			return View::make('platform/menus::widgets/nav', compact('children', 'cssClass'));
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
	 * @throws \InvalidArgumentException
	 */
	protected function getChildrenForSlug($slug, $depth = 0)
	{
		// Validate the start component
		if ( ! strlen($slug))
		{
			throw new InvalidArgumentException("Empty string was provided for the menu item which to base navigation on.");
		}

		$visibilities = array(
			'always',
			Sentry::check() ? 'logged_in' : 'logged_out',
		);

		if (Sentry::check() and Sentry::hasAccess('admin')) $visibilities[] = 'admin';

		$enabled = true;

		$response = API::get("v1/menus/{$slug}", compact('depth', 'visibilities', 'enabled'));

		return $response['children'];
	}

	/**
	 * Recursively prepares a child for presentation within the nav widget.
	 *
	 * @param  \Platform\Menus\Models\Menu  $child
	 * @param  string  $beforeUri
	 * @return void
	 */
	protected function prepareChildRecursively($child, $beforeUri = null)
	{
		// Prepare the options array
		$options = array(
			'before_uri' => $beforeUri,
		);

		// Get this item children
		$child->children = $child->getChildren();

		// Prepare the target
		$child->target = "_{$child->target}";

		// Prepare the uri
		$child->uri = $child->getUrl($options);

		// Do we have a regular expression for this item?
		if ($regex = $child->regex)
		{
			// Make sure that the regular expression is valid
			if (@preg_match($regex, $this->path))
			{
				$child->isActive = true;
			}
		}

		// Check if the uri of the item matches the current request path
		elseif ($child->uri === $this->path)
		{
			$child->isActive = true;
		}

		// Check if this item has sub items
		$child->hasSubItems = ($child->children and $child->depth > 1);

		// Recursive!
		foreach ($child->children as $grandChild)
		{
			$this->prepareChildRecursively($grandChild, $beforeUri);
		}
	}

}
