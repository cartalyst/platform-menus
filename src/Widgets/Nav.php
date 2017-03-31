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
 * @version    5.0.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Menus\Widgets;

use Exception;
use Platform\Menus\Models\Menu;
use Cartalyst\Sentinel\Sentinel;
use Platform\Menus\Repositories\MenuRepositoryInterface;

class Nav
{
    /**
     * The Menus repository.
     *
     * @var \Platform\Menus\Repositories\MenuRepositoryInterface
     */
    protected $menus;

    /**
     * The Sentinel instance.
     *
     * @var \Cartalyst\Sentinel\Sentinel
     */
    protected $sentinel;

    /**
     * Holds the current request path information.
     *
     * @var string
     */
    protected $path;

    /**
     * Constructor.
     *
     * @param  \Cartalyst\Sentinel\Sentinel  $sentinel
     * @param  \Platform\Menus\Repositories\MenuRepositoryInterface  $menus
     * @return void
     */
    public function __construct(Sentinel $sentinel, MenuRepositoryInterface $menus)
    {
        $this->menus = $menus;

        $this->sentinel = $sentinel;

        $this->path = url()->current();
    }

    /**
     * Returns navigation HTML based off the current active menu.
     *
     * @param  string  $slug
     * @param  string  $depth
     * @param  string  $cssClass
     * @param  string  $beforeUri
     * @param  string  $view
     * @return \Illuminate\View\View
     */
    public function show($slug, $depth = 0, $cssClass = null, $beforeUri = null, $view = null)
    {
        try {
            // Get the menu children
            $children = $this->getChildrenForSlug($slug, $depth);

            // Loop through and prepare the child for display
            foreach ($children as $child) {
                $this->prepareChildRecursively($child, $beforeUri);
            }

            $view = $view ?: 'platform/menus::widgets/nav';

            return view($view, compact('children', 'cssClass'));
        } catch (Exception $e) {
            return;
        }
    }

    /**
     * Returns the children for a menu with the given slug.
     *
     * @param  string  $slug
     * @param  int  $depth
     * @return array
     */
    protected function getChildrenForSlug($slug, $depth = 0)
    {
        if ($menu = $this->menus->find($slug)) {
            if (! $menu->enabled) {
                return [];
            }

            $user = $this->sentinel->check();

            $roles = $user ? $user->roles->pluck('id')->toArray() : [];

            $visibilities = array_filter([
                'always',
                $user ? 'logged_in' : 'logged_out',
                ($user && $this->sentinel->hasAnyAccess(['superuser', 'admin'])) ? 'admin' : null,
            ]);

            return $menu->findDisplayableChildren($visibilities, $roles, $depth);
        }

        return [];
    }

    /**
     * Recursively prepares a child for presentation within the nav widget.
     *
     * @param  \Platform\Menus\Models\Menu  $child
     * @param  string  $beforeUri
     * @return void
     */
    protected function prepareChildRecursively(Menu $child, $beforeUri = null)
    {
        // Prepare the options array
        $options = [
            'before_uri' => $beforeUri,
        ];

        // Get this item children
        $child->children = $child->getChildren();

        // Prepare the target
        $child->target = "_{$child->target}";

        // Store the original uri
        $originalUri = $child->uri;

        // Prepare the uri
        $child->uri = $child->getUrl($options);
        $child->uri = str_replace(':admin', admin_uri(), $child->uri);

        // Do we have a regular expression for this item?
        if ($regex = $child->regex) {
            $regex = str_replace(':admin', admin_uri(), $regex);

            // Make sure that the regular expression is valid
            if (@preg_match($regex, $this->path)) {
                $child->isActive = true;
            }
        }

        // Check if the uri of the item matches the current request path
        elseif ($originalUri != '') {
            if ($child->uri === $this->path) {
                $child->isActive = true;
            }
        }

        // Check if this item has sub items
        $child->hasSubItems = ($child->children && $child->depth > 1);

        // Recursive!
        foreach ($child->children as $grandChild) {
            $this->prepareChildRecursively($grandChild, $beforeUri);
        }
    }
}
