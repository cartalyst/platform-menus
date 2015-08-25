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
 * @version    3.0.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Menus\Repositories;

use Cartalyst\Support\Traits;
use Platform\Menus\Models\Menu;
use Illuminate\Container\Container;

class MenuRepository implements MenuRepositoryInterface
{
    use Traits\ContainerTrait, Traits\EventTrait, Traits\RepositoryTrait, Traits\ValidatorTrait;

    /**
     * The Illuminate Cache manager instance.
     *
     * @var \Illuminate\Cache\CacheManager
     */
    protected $cache;

    /**
     * The Data handler.
     *
     * @var \Platform\Menus\Handlers\DataHandlerInterface
     */
    protected $data;

    /**
     * The Eloquent menu model.
     *
     * @var string
     */
    protected $model;

    /**
     * Constructor.
     *
     * @param  \Illuminate\Container\Container  $app
     * @return void
     */
    public function __construct(Container $app)
    {
        $this->setContainer($app);

        $this->setDispatcher($app['events']);

        $this->data = $app['platform.menus.handler.data'];

        $this->setValidator($app['platform.menus.validator']);

        $this->setModel(get_class($app['Platform\Menus\Models\Menu']));
    }

    /**
     * {@inheritDoc}
     */
    public function grid()
    {
        $menus = $this->findAllRoot();

        foreach ($menus as $menu) {
            $count = $menu->getChildrenCount();

            $menu->items_count = trans_choice('platform/menus::model.general.items', $count, compact('count'));
        }

        return $menus;
    }

    /**
     * {@inheritDoc}
     */
    public function findAll()
    {
        return $this->container['cache']->rememberForever('platform.menus.all', function () {
            return $this->createModel()->findAll();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function findAllRoot()
    {
        return $this->container['cache']->rememberForever('platform.menus.all.root', function () {
            return $this->createModel()->allRoot();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function find($id)
    {
        if ($id instanceof Menu) {
            return $id;
        }

        if (is_numeric($id)) {
            return $this->container['cache']->rememberForever('platform.menu.'.$id, function () use ($id) {
                return $this->createmodel()->find($id);
            });
        }

        return $this->findBySlug($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findBySlug($slug)
    {
        return $this->container['cache']->rememberForever('platform.menu.slug.'.$slug, function () use ($slug) {
            return $this->createModel()->whereSlug($slug)->first();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function findRoot($id)
    {
        if (is_numeric($id)) {
            return $this->container['cache']->rememberForever('platform.menu.root.'.$id, function () use ($id) {
                $model = $this->createModel();

                $menu = $model->where($model->getReservedAttributeName('left'), 1);

                return $menu->find($id);
            });
        }

        return $this->findRootBySlug($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findRootBySlug($slug)
    {
        return $this->container['cache']->rememberForever('platform.menu.root.slug.'.$slug, function () use ($slug) {
            $model = $this->createModel();

            $menu = $model->where($model->getReservedAttributeName('left'), 1);

            return $menu->whereSlug($slug)->first();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function findWhere($column, $value)
    {
        return $this->createModel()->where($column, $value)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function findAllWhere($column, $value)
    {
        return $this->createModel()->where($column, $value)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getPreparedMenu($id)
    {
        // Do we have a menu identifier?
        if (isset($id)) {
            // Get the menu information
            if (! $menu = $this->findRoot($id)) {
                return false;
            }
        } else {
            $menu = $this->createModel();
        }

        // Get the persisted slugs
        $persistedSlugs = $this->slugs();

        // Get this menu children
        $children = $menu->exists ? $menu->findChildren(0) : [];

        // Get a list of all the available roles
        $roles = $this->container['platform.roles']->findAll();

        // Get all the registered menu types
        $types = $this->getTypes();

        return compact('menu', 'persistedSlugs', 'roles', 'types', 'children');
    }

    /**
     * {@inheritDoc}
     */
    public function slugs()
    {
        return array_map(function ($child) {
            return $child['slug'];
        }, $this->findAll());
    }

    /**
     * {@inheritDoc}
     */
    public function getManager()
    {
        return $this->container['platform.menus.manager'];
    }

    /**
     * {@inheritDoc}
     */
    public function getTypes()
    {
        return $this->getManager()->getTypes();
    }

    /**
     * {@inheritDoc}
     */
    public function validForCreation(array $data)
    {
        return $this->validator->on('create')->validate($data);
    }

    /**
     * {@inheritDoc}
     */
    public function validForUpdate(Menu $menu, array $data)
    {
        $bindings = [ 'slug' => $menu->slug ];

        return $this->validator->on('update')->bind($bindings)->validate($data);
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $input)
    {
        // Create a new menu
        $menu = $this->createModel();

        // Fire the 'platform.menu.creating' event
        if ($this->fireEvent('platform.menu.creating', [ $input ]) === false) {
            return false;
        }

        // Prepare the submitted data
        $data = $this->data->prepare($input);

        // Validate the submitted data
        $messages = $this->validForCreation($data);

        // Check if the validation returned any errors
        if ($messages->isEmpty()) {
            // Update the menu
            foreach (array_except($data, ['children']) as $key => $value) {
                $menu->{$key} = $value;
            }

            // Set this new menu as root
            $menu->makeRoot();

            // Set this menu children
            if ($children = array_get($data, 'children', null)) {
                $menu->mapTree($children);
            }

            // Fire the 'platform.menu.created' event
            $this->fireEvent('platform.menu.created', [ $menu ]);
        }

        return [ $messages, $menu ];
    }

    /**
     * {@inheritDoc}
     */
    public function update($id, array $input)
    {
        // Get the menu object
        $menu = $this->find($id);

        // Fire the 'platform.menu.updating' event
        if ($this->fireEvent('platform.menu.updating', [ $menu, $input ]) === false) {
            return false;
        }

        // Prepare the submitted data
        $data = $this->data->prepare($input);

        // Get the menu children
        $children = array_pull($data, 'children', []);

        // Validate the submitted data
        $messages = $this->validForUpdate($menu, $data);

        // Check if the validation returned any errors
        if ($messages->isEmpty()) {
            // Update the menu
            foreach ($data as $key => $value) {
                $menu->{$key} = $value;
            }
            $menu->save();

            // Set this menu children
            if ($children) {
                $menu->mapTree($children);
            }

            // Fire the 'platform.menu.updated' event
            $this->fireEvent('platform.menu.updated', [ $menu ]);
        }

        return [ $messages, $menu ];
    }

    /**
     * {@inheritDoc}
     */
    public function store($id, array $input)
    {
        return ! $id ? $this->create($input) : $this->update($id, $input);
    }

    /**
     * {@inheritDoc}
     */
    public function enable($id)
    {
        $this->validator->bypass();

        return $this->update($id, [ 'enabled' => true ]);
    }

    /**
     * {@inheritDoc}
     */
    public function disable($id)
    {
        $this->validator->bypass();

        return $this->update($id, [ 'enabled' => false ]);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($id)
    {
        // Check if the menu exists
        if ($menu = $this->find($id)) {
            // Fire the 'platform.menu.deleted' event
            $this->fireEvent('platform.menu.deleted', [ $menu ]);

            // Delete the menu
            $menu->deleteWithChildren();

            return true;
        }

        return false;
    }
}
