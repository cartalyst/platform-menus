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

use Cartalyst\NestedSets\Nodes\EloquentNode;
use Illuminate\Database\Query\Expression;
use InvalidArgumentException;
use Platform\Menus\Types\TypeInterface;
use RuntimeException;

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

	/**
	 * Hold the type data when saving the menu.
	 *
	 * @var array
	 */
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
	 * @throws \InvalidArgumentException
	 */
	public function getGroupsAttribute($groups)
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
			throw new InvalidArgumentException("Cannot JSON decode groups [{$groups}].");
		}

		return $_groups;
	}

	/**
	 * Set mutator for the "groups" attribute.
	 *
	 * @param  array  $groups
	 * @return void
	 */
	public function setGroupsAttribute($groups)
	{
		// If we get a string, let's just ensure it's a proper JSON string
		if ( ! is_array($groups))
		{
			$groups = $this->getGroupsAttribute($groups);
		}

		if ( ! empty($groups))
		{
			$groups = array_values(array_map('intval', $groups));
			$this->attributes['groups'] = json_encode($groups);
		}
		else
		{
			$this->attributes['groups'] = '';
		}
	}

	/**
	 * Filters children and returns an array of children
	 * which satisfy any of the provided visibilities.
	 *
	 * @param  array  $visibilities
	 * @param  array  $groups
	 * @param  bool   $enabled
	 * @param  int    $depth
	 * @return array
	 */
	public function findDisplayableChildren(array $visibilities, array $groups = null, $enabled = null, $depth = 0)
	{
		$worker = $this->createWorker();

		$children = $this->filterChildren(function($query) use ($visibilities, $groups, $enabled, $worker)
		{
			$query->whereIn(
				new Expression($worker->wrapColumn('node.visibility')),
				$visibilities
			);

			// If we have groups set, we'll filter down to records who are likely
			// to contain our group. This will speed up the filtering process
			// later on.
			if (isset($groups))
			{
				$query->whereNested(function($query) use ($groups, $worker)
				{
					foreach ($groups as $group)
					{
						$query->orWhere(
							new Expression($worker->wrapColumn("node.groups")),
							'LIKE',
							$group
						);
					}

					$query->orWhere(
						new Expression($worker->wrapColumn("node.groups")),
						''
					)
					->orWhereNull(
						new Expression($worker->wrapColumn("node.groups"))
					);
				});
			}

			if ( ! is_null($enabled))
			{
				$query->where(
					new Expression($worker->wrapColumn('node.enabled')),
					'=',
					$enabled
				);
			}
		}, $depth);

		$this->filterChildrenGroups($children, $groups);

		return $children;
	}

	protected function filterChildrenGroups(array &$children, array $groups = null)
	{
		if ( ! isset($groups))
		{
			return $children;
		}

		foreach ($children as $index => $child)
		{
			if (count($child->groups) > 0)
			{
				$matching = array_intersect($child->groups, $groups);

				if (count($matching) === 0)
				{
					unset($children[$index]);
					continue;
				}
			}

			$grandChildren = $child->getChildren();
			$this->filterChildrenGroups($grandChildren, $groups);
			$child->setChildren($grandChildren);
		}
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
	 * Return information about the provided type.
	 *
	 * @param  string  $type
	 * @return array
	 * @throws \RuntimeException
	 */
	public function getType($type = null)
	{
		$type = $type ?: $this->type;

		if (is_null($type)) return false;

		if ( ! isset(static::$types[$type]))
		{
			throw new RuntimeException("Menu type [{$type}] has not been registered.");
		}

		return static::$types[$type];
	}

	/**
	 * Register a custom type with a menu.
	 *
	 * @param  \Platform\Menus\Types\TypeInterface  $type
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
	 * Return the type data.
	 *
	 * @return array
	 */
	public function getTypeData()
	{
		return $this->typeData;
	}

	/**
	 * Set the type data.
	 *
	 * @return void
	 */
	public function setTypeData(array $typeData)
	{
		$this->typeData = $typeData;
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
		$type = $this->getType();

		$methodFound = false;

		$_method = 'get'.substr($method, 3).'Attribute';

		if (method_exists($type, $method))
		{
			$methodFound = true;
		}
		elseif (method_exists($type, $_method))
		{
			$method = $_method;

			$methodFound = true;
		}

		if ($methodFound)
		{
			array_unshift($parameters, $this);

			return call_user_func_array(array($type, $method), $parameters);
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
