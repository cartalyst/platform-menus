<?php namespace Platform\Menus\Models;
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

use Cartalyst\NestedSets\Nodes\EloquentNode;
use Illuminate\Database\Query\Expression;

class Menu extends EloquentNode {

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
	protected $primaryKey = 'id';

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = array('lft', 'rgt', 'menu', 'depth', 'created_at', 'updated_at');

	/**
	 * Array of attributes reserved for the worker. These attributes
	 * cannot be set publically, only internally and shouldn't
	 * really be set outside this class.
	 *
	 * @var array
	 */
	protected $reservedAttributes = array(
		'left'  => 'lft',
		'right' => 'rgt',
		'tree'  => 'menu',
	);

	/**
	 * Get mutator for the enabled attribute.
	 *
	 * @param  mixed  $enabled
	 * @return bool
	 */
	public function getEnabledAttribute($enabled)
	{
		return (bool) $enabled;
	}

	/**
	 * Get mutator for the secure attribute.
	 *
	 * @param  mixed  $secure
	 * @return bool
	 */
	public function getSecureAttribute($secure)
	{
		return (bool) $secure;
	}

	/**
	 * Filters children and returns an array of children
	 * which satisfy any of the provided visibilities.
	 *
	 * @param  array  $visibilities
	 * @param  bool   $enabled
	 * @param  int    $depth
	 * @return array
	 */
	public function findDisplayableChildren(array $visibilities, $enabled = null, $depth = 0)
	{
		$worker = $this->createWorker();

		return $this->filterChildren(function($query) use ($visibilities, $enabled, $worker)
		{
			$query->whereIn(
				new Expression($worker->wrapColumn('node.visibility')),
				$visibilities
			);

			if ( ! is_null($enabled))
			{
				$query->where(
					new Expression($worker->wrapColumn('node.enabled')),
					'=',
					$enabled
				);
			}
		}, $depth);
	}

	/**
	 * Filters children and returns an array of enabled
	 * children only.
	 *
	 * @param  int  $depth
	 * @return array
	 */
	public function findEnabledChildren($depth = 0)
	{
		$worker = $this->createWorker();

		return $this->filterChildren(function($query) use ($worker)
		{
			$query->where(
				new Expression($worker->wrapColumn('node.enabled')),
				'=',
				1
			);
		}, $depth);
	}

	/**
	 * Return the guarded attributes.
	 *
	 * @return array
	 */
	public function getGuarded()
	{
		return $this->guarded;
	}

	/**
	 * Find a model by its primary key.
	 *
	 * @param  mixed  $id
	 * @param  array  $columns
	 * @return \Illuminate\Database\Eloquent\Model|Collection
	 */
	public static function find($id, $columns = array('*'))
	{
		$instance = new static;

		if ( ! is_numeric($id))
		{
			return $instance->newQuery()->where('slug', '=', $id)->first($columns);
		}

		return parent::find($id, $columns);
	}

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
			if (is_null($menu = static::find($slug)))
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
