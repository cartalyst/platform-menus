<?php namespace Platform\Menus\Handlers;
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

class DataHandler implements DataHandlerInterface {

	/**
	 * @{inheritDoc}
	 */
	public function prepare(array $data)
	{
		// Get the tree
		$tree = json_decode(request()->get('menu-tree', []), true);

		// Prepare our children
		$children = [];

		foreach ($tree as $child)
		{
			// Ensure no bad data is coming through from POST
			if ( ! is_array($child)) continue;

			$this->processChildRecursively($child, $children);
		}

		// Prepare the menu data for the API
		return [
			'children' => $children,
			'slug'     => request()->get('menu-slug'),
			'name'     => request()->get('menu-name'),
		];
	}


	/**
	 * Recursively processes a child node by extracting POST data
	 * from the admin UI so that we may structure a nice tree of
	 * pure data to send off to the API.
	 *
	 * @param  array  $child
	 * @param  array  $children
	 * @return void
	 */
	protected function processChildRecursively($child, &$children)
	{
		// Existing menu children will be passing an ID to us. This
		// is advantageous to us, since a menu slug can be changed
		// without anything being messed up.
		$index = $child['id'];

		$new_child = [
			'name'       => request()->get("children.{$index}.name"),
			'slug'       => request()->get("children.{$index}.slug"),
			'enabled'    => request()->get("children.{$index}.enabled", 1),
			'type'       => $type = request()->get("children.{$index}.type", 'static'),
			'secure'     => request()->get("children.{$index}.secure", 0),
			'visibility' => request()->get("children.{$index}.visibility", 'always'),
			'roles'      => (array) request()->get("children.{$index}.roles", []),
			'class'      => request()->get("children.{$index}.class"),
			'target'     => request()->get("children.{$index}.target"),
			'regex'      => request()->get("children.{$index}.regex"),
		];

		// Only append id if we are dealing with
		// an existing menu item.
		if (is_numeric($index))
		{
			$new_child['id'] = $index;
		}

		// Attach the type data
		$new_child = array_merge($new_child, request()->get("children.{$index}.{$type}", []));

		// If we have children, call the function again
		if ( ! empty($child['children']) and is_array($child['children']) and count($child['children']) > 0)
		{
			$grand_children = [];

			foreach ($child['children'] as $child)
			{
				$this->processChildRecursively($child, $grand_children);
			}

			$new_child['children'] = $grand_children;
		}

		$children[] = $new_child;
	}

}
