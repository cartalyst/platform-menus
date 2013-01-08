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

class Menu extends \Cartalyst\Nesty\Model {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'menus';

	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'slug';

	/**
	 * Indicates if the IDs are auto-incrementing.
	 *
	 * @var bool
	 */
	public $incrementing = false;

	/**
	 * Array of attributes reserved for the
	 * worker. These attributes cannot be set
	 * publically, only internally and shouldn't
	 * really be set outside this class.
	 *
	 * @var array
	 */
	protected $nestyAttributes = array(
		'left'  => 'lft',
		'right' => 'rgt',
		'tree'  => 'menu',
	);

	/**
	 * Handle dynamic static method calls into the method.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters)
	{
		// If we're making a call to a menu
		if (ends_with($method, 'Menu'))
		{
			// Determine the slug the person was after
			$slug = str_replace('_', '-', snake_case(substr($method, 0, -4)));

			// Lazily create the menu item
			if (($menu = static::find($slug)) === null)
			{
				$menu = new static(array(
					'slug' => $slug,
					'name' => ucwords(str_replace('-', ' ', $slug))
				));

				$menu->makeRoot();
			}

			return $menu;
		}

		return parent::__callStatic($method, $parameters);
	}

}