<?php namespace Platform\Menus\Types;
/**
 * Part of the Platform Menus extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Platform Menus extension
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Platform\Menus\Models\Menu;

class StaticType extends BaseType implements TypeInterface {

	/**
	 * Get the type identifier.
	 *
	 * @return string
	 */
	public function getIdentifier()
	{
		return 'static';
	}

	/**
	 * Event that is called after a menu children is saved.
	 *
	 * @param  \Platform\Menus\Menu  $child
	 * @return void
	 */
	public function afterSave(Menu $child)
	{
		$data = $child->getTypeData();

		if ($uri = array_get($data, 'uri'))
		{
			$child->uri = $data['uri'];
		}
	}

	/**
	 * Event that is called before a children is deleted.
	 *
	 * @param  \Platform\Menus\Menu  $child
	 * @return void
	 */
	public function beforeDelete(Menu $child) {}

}
