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
				if ($activeMenu = get_active_menu())
				{
					$response   = API::get("v1/menus/$activeMenu/path");
					$activePath = $response['path'];
				}

				// If the "start" property is a string, it's
				// the slug of the menu which to render
				$children = $this->getChildrenForSlug($identifier, $depth);
			}
			else
			{
				// Active menu is required for path based output
				if ( ! $activeMenu = get_active_menu())
				{
					throw new \RuntimeException("No active menu child has been set, cannot show navigation based on active menu child's path at depth [$identifier].");
				}

				$response   = API::get("v1/menus/$activeMenu/path");
				$activePath = $response['path'];

				if ( ! isset($activePath[$identifier]))
				{
					return '';

					// Let's help the user out by formatting the path
					// for them.
					array_walk($activePath, function(&$slug, $index)
					{
						$slug = "$index => '$slug'";
					});

					throw new \InvalidArgumentException(sprintf(
						'Path index of [%d] does not exist on active menu path [%s].',
						$identifier,
						implode(', ', $activePath)
					));
				}

				$children = $this->getChildrenForSlug($activePath[$identifier], $depth);
			}

			// Loop through and prepare the child for display
			foreach ($children as $child)
			{
				$this->prepareChildRecursively($child, $beforeUri, $activePath);
			}

			// Remove children that are hidden from stack
			$children = $this->removeInvisibleChildrenRecursively($children);
		}
		catch (ApiHttpException $e)
		{
			return '';
		}

		return View::make('platform/menus::widgets/nav', compact('children', 'cssClass'));
	}

	/**
	 * Returns the children for a menu with the given slug.
	 *
	 * @param  string  $slug
	 * @param  int     $depth
	 * @return array   $children
	 */
	protected function getChildrenForSlug($slug, $depth = 0)
	{
		// Validate the start compontent
		if ( ! strlen($slug))
		{
			throw new \InvalidArgumentException("Empty string was provided for the menu item which to base navigation on.");
		}

		$enabled  = true;
		$response = API::get("v1/menus/$slug/children", compact('depth', 'enabled'));
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
		switch ($child->type)
		{
			// If the child is static, we are able to prepare it right away.
			case 'static':

				// We'll modify the URI only if
				// necessary.
				if (isset($beforeUri))
				{
					$child->uri = "{$beforeUri}/{$child->uri}";
				}

				$child->in_active_path = in_array($child->id, $activePath);

				break;

			// We'll fire an event for the logic to be handled by the correct type.
			default:
				\Event::fire("platform.menus.nav.prepare_child.{$child->type}", array('child' => $child, 'beforeUri' => $beforeUri));
				break;
		}

		switch($child->visibility)
        {
            case "logged_in":
                $child->visible = Sentry::check();
                break;
            case "logged_out":
                $child->visible = !Sentry::check();
                break;
            case "admin":
                $child->visible = (Sentry::check() && Sentry::hasAccess('admin'));
                break;
                break;
            default:
                $child->visible = true;
                break;
        }

		// Recursive!
		foreach ($child->getChildren() as $grandChild)
		{
			$this->prepareChildRecursively($grandChild, $beforeUri, $activePath);
		}
	}

	 /**
     * Recursively remove any menu child that should not be
     * visible
     *
     * @param $children
     * @return mixed
     */
    protected function removeInvisibleChildrenRecursively($children)
    {
        foreach( $children as $key => $child )
        {
            $attributes = $children[$key]->getAttributes();
            if ( isset($attributes['visible']) && !$attributes['visible']) {
                unset($children[$key]);
            } else {
                if (sizeof($child->getChildren())) {
                    $children[$key] = $this->removeInvisibleChildrenRecursively($child->getChildren());
                }
            }
        }
        return $children;
    }

}

}
