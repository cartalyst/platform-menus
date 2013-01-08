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

use Api;

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
	 * @param  string   $depth
	 * @param  string  $cssClass
	 * @param  string  $beforeUri
	 */
	public function show($start = 0, $depth = 0, $cssClass = null, $beforeUri = null)
	{
		if ( ! is_numeric($start))
		{
			if ( ! strlen($start))
			{
				throw new \InvalidArgumentException("Empty string was provided for the menu item which to base navigation on.");
			}
		}
	}

}