<?php namespace Platform\Menus;
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
use Cartalyst\Extensions\ExtensionInterface;
use Platform\Ui\Models\Menu;

class Observer {

	/**
	 * Observer after an extension is enabled.
	 *
	 * @param  Cartalyst\Extensions\ExtensionInterface  $extension
	 * @return void
	 */
	public function afterEnable(ExtensionInterface $extension)
	{
		$response = API::get('menus', array('extension' => $extension->getSlug()));
		$menus = $response['menus'];

		foreach ($menus as $menu)
		{
			API::put("menus/{$menu->id}", array('menu' => array('enabled' => true)));
		}
	}

	/**
	 * Observer after an extension is disabled.
	 *
	 * @param  Cartalyst\Extensions\ExtensionInterface  $extension
	 * @return void
	 */
	public function afterDisable(ExtensionInterface $extension)
	{
		$response = API::get('menus', array('extension' => $extension->getSlug()));
		$menus = $response['menus'];

		foreach ($menus as $menu)
		{
			API::put("menus/{$menu->id}", array('menu' => array('enabled' => false)));
		}
	}

}
