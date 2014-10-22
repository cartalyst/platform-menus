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

use RuntimeException;
use InvalidArgumentException;
use Platform\Menus\Types\TypeInterface;
use Cartalyst\Attributes\EntityInterface;
use Illuminate\Database\Query\Expression;
use Platform\Attributes\Traits\EntityTrait;
use Cartalyst\NestedSets\Nodes\EloquentNode;
use Cartalyst\NestedSets\Nodes\NodeInterface;
use Cartalyst\Support\Traits\NamespacedEntityTrait;

class Menu extends EloquentNode implements EntityInterface, NodeInterface {

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
	protected $guarded = [
		'lft',
		'rgt',
		'menu',
		'depth',
		'created_at',
		'updated_at',
	];

	/**
	 * Array of attributes reserved for the worker. These attributes
	 * cannot be set publically, only internally and shouldn't
	 * really be set outside this class.
	 *
	 * @var array
	 */
	protected $reservedAttributes = [
		'left'  => 'lft',
		'right' => 'rgt',
		'tree'  => 'menu',
	];

	/**
	 * {@inheritDoc}
	 */
	protected static $entityNamespace = 'platform/menus';

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
	 */
	public function getRolesAttribute($roles)
	{
		return $roles ? json_decode($roles, true) : [];
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
			$this->attributes['roles'] = json_encode(
				array_values(array_map('intval', $roles))
			);
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
	 * @param  int  $depth
	 * @return array
	 */
	public function findDisplayableChildren(array $visibilities, array $roles = [], $depth = 0)
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
			if ( ! empty($roles))
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
	protected function filterChildrenRoles(array &$children, array $roles = [])
	{
		if (empty($roles)) return;

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
	public static function find($id, $columns = ['*'])
	{
		if (is_numeric($id)) return parent::find($id, $columns);

		$instance = new static;

		return $instance->newQuery()->where('slug', '=', $id)->first($columns);
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

		$types = app('platform.menus.manager')->getTypes();

		if ( ! array_key_exists($type, $types))
		{
			throw new RuntimeException("Menu type [{$type}] has not been registered.");
		}

		return $types[$type];
	}

	/**
	 * Handle dynamic method calls into the method.
	 *
	 * @param  string  $method
	 * @param  array  $parameters
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
	 * @param  array  $parameters
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
