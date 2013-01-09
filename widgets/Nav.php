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

class Nav {

	/**
	 * Returns navigation HTML based off the current active menu.
	 *
	 * If the start is an integer, it's the depth from the top
	 * level item based on the current active menu item.
	 *
	 * If it's a string, it's the slug of the item to start
	 * rendering from, irrespective of the active menu item.
	 *
	 * @param  string|int  $start,
	 * @param  string      $depth
	 * @param  string      $cssClass
	 * @param  string      $beforeUri
	 */
	public function show($start = 0, $depth = 0, $cssClass = null, $beforeUri = null)
	{
		if (is_numeric($start))
		{
			if ( ! $activeMenu = get_active_menu())
			{
				throw new \RuntimeException("No active menu child has been set, cannot show navigation based on active menu child's path at depth [$start].");
			}

			$result = API::get("menus/$activeMenu/path");
			$path   = $result['path'];
			unset($result);

			if ( ! isset($path[$start]))
			{
				// Let's help the user out by formatting the path
				// for them.
				array_walk($path, function(&$slug, $index)
				{
					$slug = "$index => '$slug'";
				});

				throw new \InvalidArgumentException(sprintf(
					'Path index of [%d] does not exist on active menu path [%s].',
					$start,
					implode(', ', $path)
				));
			}

			// Now we have a slug in the active path, we'll simply call
			// the method again
			return $this->show($path[$start], $depth, $cssClass, $beforeUri);
		}

		// Validate the start compontent
		if ( ! strlen($start))
		{
			throw new \InvalidArgumentException("Empty string was provided for the menu item which to base navigation on.");
		}

		$result   = API::get("menus/$start/children", array(
			'depth' => $depth,
		));
		$children = $result['children'];
		unset($result);

		// Loop through and prepare the child for display
		foreach ($children as $child)
		{
			$this->prepareChildRecursively($child, $beforeUri);
		}

		return \View::make('platform/menus::widgets/nav', compact('children', 'cssClass'));
	}

	/**
	 * Recursively prepares a child for presentation within
	 * the nav widget.
	 *
	 * If the driver type is anything but 'static', we'll fire
	 * an event for the correct extension to handle the logic
	 * of preparing the item for display.
	 *
	 * @param  Platform\Menus\Menu  $child
	 * @param  string  $beforeUri
	 * @return void
	 */
	protected function prepareChildRecursively($child, $beforeUri = null)
	{
		switch ($child->driver)
		{
			// If the child is static, we are able to prepare it right away.
			case 'static':

				// We'll modify the URI only if
				// necessary.
				if (isset($beforeUri))
				{
					$child->uri = "{$beforeUri}/{$child->uri}";
				}

				break;
			
			// We'll fire an event for the logic to be handled by the correct
			// driver.
			default:
				\Event::fire("platform.menus.nav.prepare_child.$driver", array('child' => $child, 'beforeUri' => $beforeUri));
				break;
		}

		// Recursive!
		foreach ($child->children as $grandChild)
		{
			$this->prepareChildRecursively($grandChild, $beforeUri);
		}
	}

}