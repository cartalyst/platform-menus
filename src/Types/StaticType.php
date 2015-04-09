<?php namespace Platform\Menus\Types;
/**
 * Part of the Platform Menus extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Platform Menus extension
 * @version    2.0.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Platform\Menus\Models\Menu;

class StaticType extends AbstractType implements TypeInterface {

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
	 * {@inheritDoc}
	 */
	public function afterSave(Menu $child) {}

	/**
	 * {@inheritDoc}
	 */
	public function beforeDelete(Menu $child) {}

}
