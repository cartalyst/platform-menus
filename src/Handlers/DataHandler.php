<?php

/*
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
 * @version    11.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2022, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Menus\Handlers;

use Illuminate\Support\Arr;

class DataHandler implements DataHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepare(array $data)
    {
        if (! Arr::get($data, 'menu-name')) {
            return $data;
        }

        return [
            'name'     => Arr::get($data, 'menu-name'),
            'slug'     => Arr::get($data, 'menu-slug'),
            'children' => $this->processSubmittedTree($data),
        ];
    }

    /**
     * Processes the submitted menu tree data.
     *
     * @param array $data
     *
     * @return array
     */
    protected function processSubmittedTree(array $data)
    {
        // Get the tree
        $tree = json_decode(Arr::get($data, 'menu-tree', ''), true) ?: [];

        // Prepare our children
        $children = [];

        foreach ($tree as $child) {
            if (is_array($child)) {
                $this->processChildRecursively($child, $children);
            }
        }

        return $children;
    }

    /**
     * Recursively processes the child node by extracting
     * POST data so that we can structure a nice tree of
     * pure data to send off to the API.
     *
     * @param array $child
     * @param array $children
     *
     * @return void
     */
    protected function processChildRecursively($child, &$children)
    {
        // Existing menu children will be passing an ID to us. This
        // is advantageous to us, since a menu slug can be changed
        // without anything being messed up.
        $index = $child['id'];

        $data = request()->input("children.{$index}");

        // Prepare the new child data
        $prepared = [
            'name'       => Arr::get($data, 'name'),
            'slug'       => Arr::get($data, 'slug'),
            'enabled'    => Arr::get($data, 'enabled', 1),
            'type'       => $type = Arr::get($data, 'type', 'static'),
            'secure'     => Arr::get($data, 'secure'),
            'visibility' => Arr::get($data, 'visibility', 'always'),
            'roles'      => (array) Arr::get($data, 'roles', []),
            'class'      => Arr::get($data, 'class'),
            'target'     => Arr::get($data, 'target'),
            'regex'      => Arr::get($data, 'regex'),
        ];

        // Only append the menu item id if we are
        // dealing with an existing menu item.
        if (is_numeric($index)) {
            $prepared['id'] = $index;
        }

        // Attach the menu type data
        $prepared = array_merge($prepared, Arr::get($data, $type, []));

        // If we have children, call the function again
        if (! empty($child['children']) && is_array($child['children']) && count($child['children']) > 0) {
            $grand_children = [];

            foreach ($child['children'] as $child) {
                $this->processChildRecursively($child, $grand_children);
            }

            $prepared['children'] = $grand_children;
        }

        $children[] = $prepared;
    }
}
