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
use InvalidArgumentException;
use Platform\Menus\TypeInterface;

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
	 * Array of registered type relationships, where the key is the type
	 * (which is the relationship) and the value is a closure to resolve
	 * the relationship.
	 *
	 * @var array
	 */
	protected static $types = array();


	protected $typeData = array();


	/**
	 * Get mutator for the "enabled" attribute.
	 *
	 * @param  mixed  $enabled
	 * @return bool
	 */
	public function getEnabledAttribute($enabled)
	{
		return (bool) $enabled;
	}

	/**
	 * Get mutator for the "secure" attribute.
	 *
	 * @param  mixed  $secure
	 * @return bool
	 */
	public function getSecureAttribute($secure)
	{
		return (bool) $secure;
	}

	/**
	 * Get mutator for the "groups" attribute.
	 *
	 * @param  mixed  $groups
	 * @return array
	 * @throws InvalidArgumentException
	 */
	public function _getGroupsAttribute($groups)
	{
		if ( ! $groups)
		{
			return array();
		}

		if (is_array($groups))
		{
			return $groups;
		}

		if ( ! $_groups = json_decode($groups, true))
		{
			throw new InvalidArgumentException("Cannot JSON decode groups [$groups].");
		}

		return $_groups;
	}

	/**
	 * Set mutator for the "groups" attribute.
	 *
	 * @param  array  $groups
	 * @return void
	 */
	public function _setGroupsAttribute($groups)
	{
		$groups = array_merge($this->getGroups(), $groups);

		$this->attributes['groups'] = ! empty($groups) ? json_encode($groups) : '';
	}

	public function getGroups()
	{
		return $this->groups;
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


	public function getType($type = null)
	{
		if ($type = $this->type)
		{
			if ( ! isset(static::$types[$type]))
			{
				throw new \RuntimeException("Menu type [$type] has not been registered.");
			}

			return static::$types[$type];
		}
	}

	public function _setTypeData(array $typeData)
	{
		$this->typeData = $typeData;
	}

	public function _getTypeData()
	{
		return $this->typeData;
	}


	/**
	 * Register a custom type with a menu.
	 *
	 * @param  \Platform\Menus\TypeInterface  $type
	 * @return void
	 */
	public static function registerType(TypeInterface $type)
	{
		static::$types[$type->getIdentifier()] = $type;
	}

	/**
	 * Return all the registered types.
	 *
	 * @return array
	 */
	public static function getTypes()
	{
		return static::$types;
	}

	/**
	 * Handle dynamic method calls into the method.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		$typeMethods = array(
			'getName', 'getUrl',
			'getNewTemplate', 'getNewFormTemplate',
			'getEditTemplate', 'getEditFormTemplate',
		);

		if (in_array($method, $typeMethods))
		{
			if (in_array($method, array('getName', 'getUrl')))
			{
				$method = 'getChild'.substr($method, 3);
			}

			array_unshift($parameters, $this);

			return call_user_func_array(array($this->getType(), $method), $parameters);
		}

		return parent::__call($method, $parameters);
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
