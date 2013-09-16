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
use View;

class Nav {

	/**
	 * Returns navigation HTML based off the current active menu.
	 *
	 * If the identifier is an integer, it's the depth from the top
	 * level item based on the current active menu item.
	 *
	 * If it's a string, it's the slug of the item to start
	 * rendering from, irrespective of the active menu item.
	 *
	 * @param  string|int  $identifier,
	 * @param  string      $depth
	 * @param  string      $cssClass
	 * @param  string      $beforeUri
	 * @return View
	 * @throws RuntimeException
	 */
	public function show($identifier = 0, $depth = 0, $cssClass = null, $beforeUri = null)
	{
		try
		{
			// Fallback active path
			$activePath = array();

			if ( ! is_numeric($identifier))
			{
				// If we have an active menu, we'll fill out the path now
				if (is_array($activeMenu = get_active_menu()))
				{
					foreach ($activeMenu as $activeChild)
					{
						$response     = API::get("v1/menus/{$activeChild}/path");
						$activePath[] = $response['path'];
					}
				}
				else
				{
					if($activeMenu)
					{
						$response   = API::get("v1/menus/{$activeMenu}/path");
						$activePath = $response['path'];
					}
				}

				// If the "start" property is a string, it's
				// the slug of the menu which to render.
				$children = $this->getChildrenForSlug($identifier, $depth);
			}
			else
			{
				// Active menu is required for path based output
				if ( ! $activeMenu = get_active_menu())
				{
					throw new RuntimeException("No active menu child has been set, cannot show navigation based on active menu child's path at depth [$identifier].");
				}

				$response   = API::get("v1/menus/{$activeMenu}/path");
				$activePath = $response['path'];

				if ( ! isset($activePath[$identifier]))
				{
					return '';
				}

				$children = $this->getChildrenForSlug($activePath[$identifier], $depth);
			}

			// Loop through and prepare the child for display
			foreach ($children as $child)
			{
				$this->prepareChildRecursively($child, $beforeUri, $activePath);
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

		$response = API::get("v1/menus/{$slug}/children", compact('depth', 'visibilities', 'enabled'));
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
	 * @param  array   $activePath
	 * @return void
	 */
	protected function prepareChildRecursively($child, $beforeUri = null, array $activePath = array())
	{
		$path = Request::getPathInfo();

		switch ($child->type)
		{
			// If the child is static, we are able to prepare it right away
			case 'static':

				// We'll modify the URI only if necessary
				if (isset($beforeUri))
				{
					$child->uri = "{$beforeUri}/{$child->uri}";
				}

				if ($activePath and is_array($activePath[0]))
				{
					foreach ($activePath as $currentPath)
					{
						if (in_array($child->id, $currentPath))
						{
							$child->in_active_path = in_array($child->id, $currentPath);
						}
					}
				}
				elseif ($child->uri === '/' and $path === '/')
				{
					$child->in_active_path = true;
				}
				else
				{
					$childUri = str_replace('/', '\/', $child->uri);

					if ($child->uri != '/' and preg_match("/{$childUri}/i", $path))
					{
						$child->in_active_path = true;
					}
					else
					{
						$child->in_active_path = in_array($child->id, $activePath);
					}
				}

				break;

			case 'page':

				$childUri = str_replace('/', '\/', $child->uri);

				if ($child->uri != '/' and preg_match("/{$childUri}/i", $path))
				{
					$child->in_active_path = true;
				}

				break;

			// We'll fire an event for the logic to be handled by the correct type
			default:

				Event::fire("platform.menus.nav.prepare_child.{$child->type}", array('child' => $child, 'beforeUri' => $beforeUri));

				break;
		}

		// Recursive!
		foreach ($child->getChildren() as $grandChild)
		{
			$this->prepareChildRecursively($grandChild, $beforeUri, $activePath);
		}
	}

}
