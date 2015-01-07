<?php namespace Platform\Menus\Models;
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
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

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
	 * Get accessor for the "enabled" attribute.
	 *
	 * @param  mixed  $enabled
	 * @return bool
	 */
	public function getEnabledAttribute($enabled)
	{
		return (bool) $enabled;
	}

	/**
	 * Get accessor for the "secure" attribute.
	 *
	 * @param  mixed  $secure
	 * @return bool
	 */
	public function getSecureAttribute($secure)
	{
		if ( ! is_null($secure))
		{
			return (bool) $secure;
		}
	}

	/**
	 * Get accessor for the "secure" attribute.
	 *
	 * @param  mixed  $secure
	 * @return void
	 */
	public function setSecureAttribute($secure)
	{
		if (strlen($secure) === 0)
		{
			$secure = null;
		}

		$this->attributes['secure'] = $secure;
	}

	/**
	 * Get accessor for the "roles" attribute.
	 *
	 * @param  string  $roles
	 * @return array
	 */
	public function getRolesAttribute($roles)
	{
		return json_decode($roles, true) ?: [];
	}

	/**
	 * Set mutator for the "roles" attribute.
	 *
	 * @param  array  $roles
	 * @return void
	 */
	public function setRolesAttribute($roles)
	{
		if ( ! is_array($roles)) $roles = $this->getRolesAttribute($roles);

		$roles = ! empty($roles) ? json_encode(array_values(array_map('intval', $roles))) : '';

		$this->attributes['roles'] = $roles;
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
							new Expression($worker->wrapColumn('node.roles')),
							'LIKE',
							"%{$role}%"
						);
					}

					$query->orWhere(
						new Expression($worker->wrapColumn('node.roles')),
						''
					)
					->orWhereNull(
						new Expression($worker->wrapColumn('node.roles'))
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
	 * Return information about the provided type.
	 *
	 * @return array
	 */
	public function getType()
	{
		return app('platform.menus.manager')->getType($this->type);
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

			return call_user_func_array([ $type, $method ], $parameters);
		}

		return parent::__call($method, $parameters);
	}

}
