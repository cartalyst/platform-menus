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
		$tree = json_decode(array_get($data, 'menu-tree', []), true);

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
			'slug'     => array_get($data, 'menu-slug'),
			'name'     => array_get($data, 'menu-name'),
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
			'name'       => input("children.{$index}.name"),
			'slug'       => input("children.{$index}.slug"),
			'enabled'    => input("children.{$index}.enabled", 1),
			'type'       => $type = input("children.{$index}.type", 'static'),
			'secure'     => input("children.{$index}.secure", 0),
			'visibility' => input("children.{$index}.visibility", 'always'),
			'roles'      => (array) input("children.{$index}.roles", []),
			'class'      => input("children.{$index}.class"),
			'target'     => input("children.{$index}.target"),
			'regex'      => input("children.{$index}.regex"),
		];

		// Only append id if we are dealing with
		// an existing menu item.
		if (is_numeric($index))
		{
			$new_child['id'] = $index;
		}

		// Attach the type data
		$new_child = array_merge($new_child, input("children.{$index}.{$type}", []));

		// If we have children, call the function again
		if ( ! empty($child['children']) && is_array($child['children']) && count($child['children']) > 0)
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
