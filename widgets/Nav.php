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
use Event;
use InvalidArgumentException;
use Request;
use RuntimeException;
use URL;
use View;

class Nav {

	/**
	 * Returns navigation HTML based off the current active menu.
	 *
	 * @param  string  $slug,
	 * @param  string  $depth
	 * @param  string  $cssClass
	 * @param  string  $beforeUri
	 * @return \View
	 */
	public function show($slug = 0, $depth = 0, $cssClass = null, $beforeUri = null)
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
	 * @throws InvalidArgumentException
	 */
	protected function getChildrenForSlug($slug, $depth = 0)
	{
		// Validate the start component
		if ( ! strlen($slug))
		{
			throw new \InvalidArgumentException("Empty string was provided for the menu item which to base navigation on.");
		}

		$visibilities = array(
			'always',
			Sentry::check() ? 'logged_in' : 'logged_out',
		);

		if (Sentry::check() and Sentry::hasAccess('admin')) $visibilities[] = 'admin';

		$enabled = true;

		$response = API::get("v1/menus/{$slug}", compact('depth', 'visibilities', 'enabled'));
		$children = $response['children'];

		return $children;
	}

	/**
	 * Recursively prepares a child for presentation within
	 * the nav widget.
	 *
	 * If the type is anything but 'static', we'll fire
	 * an event for the correct extension to handle the logic
	 * of preparing the item for display.
	 *
	 * @param  Platform\Menus\Menu  $child
	 * @param  string  $beforeUri
	 * @return void
	 */
	protected function prepareChildRecursively($child, $beforeUri = null)
	{
		$path = Request::getPathInfo();

		// Prepare the target
		$child->target = "_{$child->target}";

		// We'll modify the URI only if necessary
		if (isset($beforeUri))
		{
			$child->uri = "/{$beforeUri}/{$child->uri}";
		}

		if ($child->uri === $path)
		{
			$child->isActive = true;
		}

		switch ($child->type)
		{
			// We'll fire an event for the logic to be handled by the correct type
			default:

				Event::fire("platform.menus.nav.prepare_child.{$child->type}", compact('child', 'beforeUri'));

				break;
		}

		// Generate the full url
		$child->uri = $child->secure ? URL::secure($child->uri) : URL::to($child->uri);

		// Recursive!
		foreach ($child->getChildren() as $grandChild)
		{
			$this->prepareChildRecursively($grandChild, $beforeUri);
		}
	}

}
