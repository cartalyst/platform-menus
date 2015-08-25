<?php namespace Platform\Menus\Handlers;
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
 * @version    2.1.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

class DataHandler implements DataHandlerInterface {

	/**
	 * {@inheritDoc}
	 */
	public function prepare(array $data)
	{
		if ( ! array_get($data, 'menu-name')) return $data;

		return [
			'name'     => array_get($data, 'menu-name'),
			'slug'     => array_get($data, 'menu-slug'),
			'children' => $this->processSubmittedTree($data),
		];
	}

	/**
	 * Processes the submitted menu tree data.
	 *
	 * @param  array  $data
	 * @return array
	 */
	protected function processSubmittedTree(array $data)
	{
		// Get the tree
		$tree = json_decode(array_get($data, 'menu-tree', ''), true) ?: [];

		// Prepare our children
		$children = [];

		foreach ($tree as $child)
		{
			if (is_array($child))
			{
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

		$data = input("children.{$index}");

		// Prepare the new child data
		$prepared = [
			'name'       => array_get($data, 'name'),
			'slug'       => array_get($data, 'slug'),
			'enabled'    => array_get($data, 'enabled', 1),
			'type'       => $type = array_get($data, 'type', 'static'),
			'secure'     => array_get($data, 'secure'),
			'visibility' => array_get($data, 'visibility', 'always'),
			'roles'      => (array) array_get($data, 'roles', []),
			'class'      => array_get($data, 'class'),
			'target'     => array_get($data, 'target'),
			'regex'      => array_get($data, 'regex'),
		];

		// Only append the menu item id if we are
		// dealing with an existing menu item.
		if (is_numeric($index)) $prepared['id'] = $index;

		// Attach the menu type data
		$prepared = array_merge($prepared, array_get($data, $type, []));

		// If we have children, call the function again
		if ( ! empty($child['children']) && is_array($child['children']) && count($child['children']) > 0)
		{
			$grand_children = [];

			foreach ($child['children'] as $child)
			{
				$this->processChildRecursively($child, $grand_children);
			}

			$prepared['children'] = $grand_children;
		}

		$children[] = $prepared;
	}

}
