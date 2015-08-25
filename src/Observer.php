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

namespace Platform\Menus;

use Platform\Menus\Models\Menu;
use Cartalyst\Extensions\Extension;
use Illuminate\Container\Container;

class Observer
{
    /**
     * The Menus repository instance.
     *
     * @var \Platform\Menus\Repositories\MenuRepositoryInterface
     */
    protected $menus;

    /**
     * The Sentinel Roles repository instance.
     *
     * @var \Cartalyst\Sentinel\Roles\RoleRepositoryInterface
     */
    protected $roles;

    /**
     * Constructor.
     *
     * @param  \Illuminate\Container\Container  $app
     * @return void
     */
    public function __construct(Container $app)
    {
        $this->menus = $app['platform.menus'];

        $this->roles = $app['sentinel.roles'];
    }

    /**
     * Register an updating model event with the dispatcher.
     *
     * @param  \Platform\Menus\Models\Menu  $menu
     * @return void
     */
    public function updating(Menu $menu)
    {
        if ($type = $this->menus->getManager()->getType($menu->type)) {
            $type->afterSave($menu);
        }
    }

    /**
     * Observer after an extension is installed.
     *
     * @param  \Cartalyst\Extensions\Extension  $extension
     * @return void
     */
    public function afterInstall(Extension $extension)
    {
        if (! $menus = $this->extractMenus($extension)) {
            return;
        }

        foreach ($menus as $slug => $children) {
            // Now, we'll prepare the children, which will fill in
            // the blanks
            $this->prepareChildren($extension, $children);

            // Now, we'll loop through our new array and we'll compare that
            // to the children in the database. Any children in the database
            // not matching our array which are assigned to this extension
            // will be removed.
            $slugs = [];
            array_walk_recursive($children, function ($value, $key) use (&$slugs) {
                if ($key === 'slug') {
                    $slugs[] = $value;
                }
            });

            $slug = str_replace('_', '-', $slug);

            // Load up the associated menu
            if (! $menu = $this->menus->findBySlug($slug)) {
                $menu = last($this->menus->create([
                    'menu-name' => ucwords(str_replace('-', ' ', $slug)),
                    'menu-slug' => $slug,
                ]));
            }

            $menu->findChildren();

            $query = $this->menus
                ->createModel()
                ->whereMenu($menu->getKey())
                ->whereExtension($extension->getSlug())
                ->where('slug', 'like', "%{$slug}-%");

            if (count($slugs)) {
                $query->whereNotIn('slug', $slugs);
            }

            foreach ($query->get() as $child) {
                $child->refresh();

                $child->delete();
            }

            // Firstly, we'll purge the existing children (and any descendents)
            // from the children array and put them in the existing array.
            $tree     = $menu->toArray();
            $existing = $tree['children'];
            $this->recursivelyPurgeExisting($children, $existing);

            $tree = array_merge_recursive($existing, $children);
            $this->prepareChildrenAttributes($tree);

            // Because we have just taken our existing hierarchy
            // and added to it, we can save on the overhead of
            // orphaning or deleting children as there'll never
            // be any here. So, we'll just call this method as
            // a speed improvement.
            $menu->mapTreeAndKeep($tree);
        }

        $children = $this->menus->createModel()->whereExtension($extension->getSlug())->get();

        foreach ($children as $child) {
            $child->enabled = false;

            $child->save();
        }
    }

    /**
     * Observer after an extension is uninstalled.
     *
     * @param  \Cartalyst\Extensions\Extension  $extension
     * @return void
     */
    public function afterUninstall(Extension $extension)
    {
        if (! $menus = $this->extractMenus($extension)) {
            return;
        }

        foreach ($menus as $slug => $children) {
            // Build up an array of all the slugs present in the children array
            $slugs = [];
            if (is_array($children) and ! empty($children)) {
                array_walk_recursive($children, function ($value, $key) use (&$slugs) {
                    if ($key === 'slug') {
                        $slugs[] = $value;
                    }
                });
            }

            $query = $this->menus->createModel()->whereExtension($extension->getSlug());

            if (count($slugs) > 0) {
                $query->orWhereIn('slug', $slugs);
            }

            // Refresh our nodes so they're not affected by deletions and
            // remove them.
            foreach ($query->get() as $child) {
                $child->refresh();

                $child->delete();
            }
        }
    }

    /**
     * Observer after an extension is enabled.
     *
     * @param  \Cartalyst\Extensions\Extension  $extension
     * @return void
     */
    public function afterEnable(Extension $extension)
    {
        $children = $this->menus->createModel()->whereExtension($extension->getSlug())->get();

        foreach ($children as $child) {
            $child->enabled = true;

            $child->save();
        }
    }

    /**
     * Observer after an extension is disabled.
     *
     * @param  \Cartalyst\Extensions\Extension  $extension
     * @return void
     */
    public function afterDisable(Extension $extension)
    {
        $children = $this->menus->createModel()->whereExtension($extension->getSlug())->get();

        foreach ($children as $child) {
            $child->enabled = false;

            $child->save();
        }
    }

    /**
     * Extract all valid menus from an extension.php file,
     * returnin an array where the key is a menu slug and
     * the value is an array of children.
     *
     * @param  \Cartalyst\Extensions\Extension  $extension
     * @return array
     */
    protected function extractMenus(Extension $extension)
    {
        $menus = $extension->menus;

        // If the attribute physically doesn't exist, we'll just
        // skip. They may have chosen to create the items progamatically
        // so we won't want to remove everything they've done.
        if (! is_array($menus)) {
            return;
        }

        $valid = [];

        // If there's no children, we'll observe an uninstall
        // event for the child. This'll remove any children
        // in the database.
        foreach ($menus as $slug => $menu) {
            if (! is_array($menus)) {
                continue;
            }
            $valid[$slug] = $menu;
        }

        return $valid;
    }

    /**
     * Purges the existing children from the children array and placed
     * the purged items in the existing children.
     *
     * @param  array  $children
     * @param  array  $existing
     * @return void
     */
    protected function recursivelyPurgeExisting(array &$children, array &$existing)
    {
        foreach ($children as $index => &$child) {
            $this->recursivelyPurgeExisting($child['children'], $existing);

            // Now, we need to reverse-iterate through the existing
            // nodes to see if the child exists. If it does, we'll update
            // the existing nodes with it's attributes and remove it
            // from the stack. As we reverse-iterate all the way up to
            // the top, we'll be left with only the non-existent
            // children.
            if ($this->findAndUpdateExisting($child, $existing)) {
                unset($children[$index]);
            }
        }
    }

    /**
     * Attempts to place find a match for the child in the existing
     * children and update it's properties.
     *
     * @param  array  $child
     * @param  array  $existing
     * @return mixed
     */
    protected function findAndUpdateExisting(array $child, array &$existing)
    {
        foreach ($existing as $index => &$existingChild) {
            if ($result = $this->findAndUpdateExisting($child, $existingChild['children'])) {
                return $result;
            }

            if ($child['slug'] == $existingChild['slug']) {
                return $existingChild = array_merge($existingChild, array_except($child, 'children'));
            }
        }

        return null;
    }

    /**
     * Prepares children for the fancy mapping process that will occur,
     * including validating attributes.
     *
     * @param  \Cartalyst\Extensions\Extension  $extension
     * @param  array  $children
     * @return void
     */
    protected function prepareChildren(Extension $extension, &$children)
    {
        // Get the extension slug
        $slug = $extension->getSlug();

        // Prepare some sensible defaults
        $defaults = [
            'type'       => 'static',
            'visibility' => 'always',
            'children'   => [],
            'extension'  =>  $slug,
        ];

        foreach ($children as &$child) {
            $child = array_merge($defaults, $child);

            if (! isset($child['slug'])) {
                throw new \InvalidArgumentException("All menu children require a slug to be mapped from extension.php, Extension [{$slug}] has one or more slugs missing from it's menu children.");
            }

            if (! is_array($child['children'])) {
                throw new \InvalidArgumentException("Menu child [{$child['slug']}] for Extension [{$slug}] has a children property that is not an array.");
            }

            $this->prepareChildren($extension, $child['children']);
        }
    }

    /**
     * Strip the guaraded attributes from a tree of children.
     *
     * @param  array  $children
     * @return void
     */
    protected function prepareChildrenAttributes(array &$children)
    {
        // Get all the guarded attributes
        $guarded = $this->menus->createModel()->getGuarded();

        // Get all the available roles
        $roles = $this->roles->createModel()->lists('id', 'slug')->toArray();

        // Loop through the children
        foreach ($children as &$child) {
            $child = array_except($child, $guarded);

            if (isset($child['roles']) && is_array($child['roles'])) {
                $child['roles'] = array_values(array_intersect_key($roles, array_flip($child['roles'])));
            }

            // Perform a recursive call
            $this->prepareChildrenAttributes($child['children']);
        }
    }
}
