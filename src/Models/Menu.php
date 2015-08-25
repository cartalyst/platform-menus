<?php

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
 * @version    3.1.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Menus\Models;

use Illuminate\Database\Eloquent\Model;
use Cartalyst\Attributes\EntityInterface;
use Illuminate\Database\Query\Expression;
use Cartalyst\NestedSets\Nodes\NodeTrait;
use Platform\Attributes\Traits\EntityTrait;
use Illuminate\Database\Eloquent\Collection;
use Cartalyst\NestedSets\Nodes\NodeInterface;
use Cartalyst\Support\Traits\NamespacedEntityTrait;

class Menu extends Model implements EntityInterface, NodeInterface
{
    use EntityTrait, NamespacedEntityTrait, NodeTrait;

    /**
     * {@inheritDoc}
     */
    protected $table = 'menus';

    /**
     * {@inheritDoc}
     */
    protected $primaryKey = 'id';

    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'slug',
        'extension',
        'name',
        'type',
        'page_id',
        'secure',
        'uri',
        'class',
        'target',
        'visibility',
        'roles',
        'regex',
        'enabled',
    ];

    /**
     * {@inheritDoc}
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
     * The Eloquent pages model name.
     *
     * @var string
     */
    protected static $pagesModel = 'Platform\Pages\Models\Page';

    /**
     * Get accessor for the "enabled" attribute.
     *
     * @param  int  $enabled
     * @return bool
     */
    public function getEnabledAttribute($enabled)
    {
        return ($this->exists || $enabled) ? (bool) $enabled : true;
    }

    /**
     * Get accessor for the "secure" attribute.
     *
     * @param  mixed  $secure
     * @return bool
     */
    public function getSecureAttribute($secure)
    {
        if (! is_null($secure)) {
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
        if (strlen($secure) === 0) {
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
        if (! is_array($roles)) {
            $roles = $this->getRolesAttribute($roles);
        }

        $roles = ! empty($roles) ? json_encode(array_values(array_map('intval', $roles))) : '';

        $this->attributes['roles'] = $roles;
    }

    /**
     * Returns the page relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function page()
    {
        return $this->belongsTo(static::$pagesModel, 'page_id');
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

        $children = $this->filterChildren(function ($query) use ($visibilities, $roles, $worker) {
            $query->whereIn(
                new Expression($worker->wrap('node.visibility')),
                $visibilities
            );

            // If we have roles set, we'll filter down to records who are likely
            // to contain our role. This will speed up the filtering process
            // later on.
            if (! empty($roles)) {
                $query->whereNested(function ($query) use ($roles, $worker) {
                    foreach ($roles as $role) {
                        $query->orWhere(
                            new Expression($worker->wrap('node.roles')),
                            'LIKE',
                            "%{$role}%"
                        );
                    }

                    $query->orWhere(
                        new Expression($worker->wrap('node.roles')),
                        ''
                    )
                    ->orWhereNull(
                        new Expression($worker->wrap('node.roles'))
                    );
                });
            }

        }, $depth);

        Collection::make($children)->load('page');

        $this->filterChildrenPageVisibility($children, $visibilities);

        $this->filterChildrenStatus($children);

        $this->filterChildrenRoles($children, $roles);

        return $children;
    }

    /**
     * Filters children pages based on their visibility settings.
     *
     * @param  array  $children
     * @param  array  $visibilities
     * @return void
     */
    protected function filterChildrenPageVisibility(array &$children, array $visibilities = [])
    {
        if (empty($visibilities)) {
            return;
        }

        foreach ($children as $index => $child) {
            if ($page = $child->page) {
                if (! in_array($page->visibility, $visibilities)) {
                    unset($children[$index]);
                    continue;
                }
            }

            $grandChildren = $child->getChildren();
            $this->filterChildrenPageVisibility($grandChildren, $visibilities);
            $child->setChildren($grandChildren);
        }
    }

    /**
     * Filters enabled children.
     *
     * @param  array  $children
     * @return void
     */
    protected function filterChildrenStatus(array &$children)
    {
        foreach ($children as $index => $child) {
            if (! $child->enabled) {
                unset($children[$index]);
                continue;
            }

            if (($page = $child->page) && ! $page->enabled) {
                unset($children[$index]);
                continue;
            }

            if ($grandChildren = $child->children) {
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
        if (empty($roles)) {
            return;
        }

        foreach ($children as $index => $child) {
            if (count($child->roles) > 0) {
                $matching = array_intersect($child->roles, $roles);

                if (count($matching) === 0) {
                    unset($children[$index]);
                    continue;
                }

                if ($page = $child->page) {
                    if (count(array_intersect($page->roles, $roles)) === 0) {
                        unset($children[$index]);
                        continue;
                    }
                }
            }

            $grandChildren = $child->getChildren();

            $this->filterChildrenRoles($grandChildren, $roles);

            $child->setChildren($grandChildren);
        }
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

        if (method_exists($type, $method)) {
            $methodFound = true;
        } elseif (method_exists($type, $_method)) {
            $method = $_method;

            $methodFound = true;
        }

        if ($methodFound) {
            array_unshift($parameters, $this);

            return call_user_func_array([ $type, $method ], $parameters);
        }

        return parent::__call($method, $parameters);
    }
}
