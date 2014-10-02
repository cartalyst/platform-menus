<?php namespace Platform\Menus\Models;
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

use Cartalyst\Attributes\EntityInterface;
use Cartalyst\NestedSets\Nodes\EloquentNode;
use Illuminate\Database\Query\Expression;
use InvalidArgumentException;
use Platform\Attributes\Traits\EntityTrait;
use Platform\Menus\Types\TypeInterface;
use RuntimeException;
use Cartalyst\Support\Traits\NamespacedEntityTrait;

class Menu extends EloquentNode implements EntityInterface {

	use EntityTrait, NamespacedEntityTrait;

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
	 * Get mutator for the "roles" attribute.
	 *
	 * @param  mixed  $roles
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function getRolesAttribute($roles)
	{
		if ( ! $roles)
		{
			return array();
		}

		if (is_array($roles))
		{
			return $roles;
		}

		if ( ! $_roles = json_decode($roles, true))
		{
			throw new InvalidArgumentException("Cannot JSON decode roles [{$roles}].");
		}

		return $_roles;
	}

	/**
	 * Set mutator for the "roles" attribute.
	 *
	 * @param  array  $roles
	 * @return void
	 */
	public function setRolesAttribute($roles)
	{
		// If we get a string, let's just ensure it's a proper JSON string
		if ( ! is_array($roles))
		{
			$roles = $this->getRolesAttribute($roles);
		}

		if ( ! empty($roles))
		{
			$roles = array_values(array_map('intval', $roles));
			$this->attributes['roles'] = json_encode($roles);
		}
		else
		{
			$this->attributes['roles'] = '';
		}
	}

	/**
	 * Filters children and returns an array of children
	 * which satisfy any of the provided visibilities.
	 *
	 * @param  array  $visibilities
	 * @param  array  $roles
	 * @param  int    $depth
	 * @return array
	 */
	public function findDisplayableChildren(array $visibilities, array $roles = null, $depth = 0)
	{
		$worker = $this->createWorker();

		$children = $this->filterChildren(function($query) use ($visibilities, $roles, $worker)
		{
			$query->whereIn(
				new Expression($worker->wrapColumn('node.visibility')),
				$visibilities
			);

			// If we have roles set, we'll filter down to records who are likely
			// to contain our role. This will speed up the filtering process
			// later on.
			if (isset($roles))
			{
				$query->whereNested(function($query) use ($roles, $worker)
				{
					foreach ($roles as $role)
					{
						$query->orWhere(
							new Expression($worker->wrapColumn("node.roles")),
							'LIKE',
							"%{$role}%"
						);
					}

					$query->orWhere(
						new Expression($worker->wrapColumn("node.roles")),
						''
					)
					->orWhereNull(
						new Expression($worker->wrapColumn("node.roles"))
					);
				});
			}

		}, $depth);

		$this->filterChildrenStatus($children);

		$this->filterChildrenRoles($children, $roles);

		return $children;
	}

	/**
	 * Filters enabled children.
	 *
	 * @param  array  $children
	 * @return void
	 */
	protected function filterChildrenStatus(array &$children)
	{
		foreach ($children as $index => $child)
		{
			if ( ! $child->enabled)
			{
				unset($children[$index]);

				continue;
			}

			if ($grandChildren = $child->children)
			{
				$this->filterChildrenStatus($grandChildren);

				$child->setChildren($grandChildren);
			}
		}
	}

	/**
	 * Filters children based on their roles.
	 *
	 * @param  array  $children
	 * @param  array  $roles
	 * @return void
	 */
	protected function filterChildrenRoles(array &$children, array $roles = null)
	{
		if ( ! isset($roles))
		{
			return;
		}

		foreach ($children as $index => $child)
		{
			if (count($child->roles) > 0)
			{
				$matching = array_intersect($child->roles, $roles);

				if (count($matching) === 0)
				{
					unset($children[$index]);
					continue;
				}
			}

			$grandChildren = $child->getChildren();
			$this->filterChildrenRoles($grandChildren, $roles);
			$child->setChildren($grandChildren);
		}
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
