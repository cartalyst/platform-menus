<?php namespace Platform\Menus;
/**
 * Part of the Platform application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Platform
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use API;
use Cartalyst\Extensions\Extension;
use Platform\Ui\Models\Menu;

class Observer {

	/**
	 * Observer after an extension is installed.
	 *
	 * @param  Cartalyst\Extensions\Extension  $extension
	 * @return void
	 * @todo   See if we can move the fetching to the API, if not
	 *         we'll remove it from all methods.
	 */
	public function afterInstall(Extension $extension)
	{
		$children = $extension->menu;

		// If there's no children, we'll observe an uninstall
		// event for the child. This'll remove any children
		// in the database.
		if ( ! is_array($children) or empty($children))
		{
			return $this->afterUninstall($extension);
		}

		// Now, we'll prepare the children, which will fill in
		// the blanks
		$this->recursivelyPrepareChildren($extension, $children);


		// Now, we'll loop through our new array and we'll compare that
		// to the children in the database. Any children in the database
		// not matching our array which are assigned to this extension
		// will be removed.
		$slugs = array();
		array_walk_recursive($children, function($value, $key) use (&$slugs)
		{
			if ($key == 'slug') $slugs[] = $value;
		});

		$existing = with(new Menu)
		    ->newQuery()
		    ->where('extension', '=', $extension->getSlug())
		    ->whereNotIn('slug', $slugs)
		    ->get();

		foreach ($existing as $child)
		{
			$child->delete();
		}

		// Firstly, we'll purge the existing children (and any descendents)
		// from the children array and put them in the existing array.
		with($adminMenu = Menu::adminMenu())->findChildren();
		$tree     = $adminMenu->toArray();
		$existing = $tree['children'];
		$this->recursivelyPurgeExisting($children, $existing);

		$tree = array_merge($existing, $children);
		$this->recursivelyStripAttributes($tree);

		// Because we have just taken our existing hierarchy
		// and added to it, we can save on the overhead of
		// orphaning or deleting children as there'll never
		// be any here. So, we'll just call this method as
		// a speed improvement.
		$adminMenu->mapTreeAndKeep($tree);
	}

	/**
	 * Prepares children for the fancy mapping process that will occur,
	 * including validating attributes.
	 *
	 * @param  Cartalyst\Extensions\Extension  $extension
	 * @param  array  $children
	 * @return void
	 */
	protected function recursivelyPrepareChildren(Extension $extension, &$children)
	{
		foreach ($children as &$child)
		{
			if ( ! isset($child['slug']))
			{
				throw new \InvalidArgumentException("All menu children require a slug to be mapped from extension.php, Extension [{$extension->getSlug()}] has one or more slugs missing from it's menu children.");
			}

			$child['extension'] = $extension->getSlug();

			if ( ! isset($child['type'])) $child['type']         = 'static';
			if ( ! isset($child['children'])) $child['children'] = array();

			if ( ! is_array($child['children']))
			{
				throw new \InvalidArgumentException("Menu child [{$child['slug']}] for Extension [{$extension->getSlug()}] has a children property that is not an array.");
			}

			$this->recursivelyPrepareChildren($extension, $child['children']);
		}
	}

	/**
	 * Observer after an extension is uninstalled.
	 *
	 * @param  Cartalyst\Extensions\Extension  $extension
	 * @return void
	 */
	public function afterUninstall(Extension $extension)
	{
		$children = $extension->menu;

		$slugs = array();

		array_walk_recursive($children, function($value, $key) use (&$slugs)
		{
			if ($key == 'slug') $slugs[] = $value;
		});

		$existing = with(new Menu)
		    ->newQuery()
		    ->where('extension', '=', $extension->getSlug())
		    ->orWhereIn('slug', $slugs)
		    ->get();

		foreach ($existing as $child)
		{
			$child->refresh();
			$child->delete();
		}
	}

	/**
	 * Observer after an extension is enabled.
	 *
	 * @param  Cartalyst\Extensions\Extension  $extension
	 * @return void
	 */
	public function afterEnable(Extension $extension)
	{
		$response = API::get('menus', array('extension' => $extension->getSlug()));
		$menus = $response['menus'];

		foreach ($menus as $menu)
		{
			API::put("menus/{$menu->id}", array('menu' => array('enabled' => true)));
		}
	}

	/**
	 * Observer after an extension is disabled.
	 *
	 * @param  Cartalyst\Extensions\Extension  $extension
	 * @return void
	 */
	public function afterDisable(Extension $extension)
	{
		$response = API::get('menus', array('extension' => $extension->getSlug()));
		$menus = $response['menus'];

		foreach ($menus as $menu)
		{
			API::put("menus/{$menu->id}", array('menu' => array('enabled' => false)));
		}
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
		foreach ($children as $index => &$child)
		{
			$this->recursivelyPurgeExisting($child['children'], $existing);

			// Now, we need to reverse-iterate through the existing
			// nodes to see if the child exists. If it does, we'll update
			// the existing nodes with it's attributes and remove it
			// from the stack. As we reverse-iterate all the way up to
			// the top, we'll be left with only the non-existent
			// children.
			if ($this->findAndUpdateExisting($child, $existing))
			{
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
	 * @return void
	 */
	protected function findAndUpdateExisting(array $child, array &$existing)
	{
		foreach ($existing as $index => &$existingChild)
		{
			if ($result = $this->findAndUpdateExisting($child, $existingChild['children']))
			{
				return $result;
			}

			if ($child['slug'] == $existingChild['slug'])
			{
				return $existingChild = array_merge($existingChild, array_except($child, 'children'));

				// Because we are reverse-iterating, we know that any children
				// associate with the child we're currently on are new, otherwise
				// they'd have been assigned and removed. So, we just append
				// our children to those of the existing child.
				// $existingChild['children'] = array_merge($existingChild['children'], $child['children']);
			}
		}

		return null;
	}

	/**
	 * Strip the guaraded attributes from a tree of children.
	 *
	 * @param  array  $children
	 * @return void
	 */
	protected function recursivelyStripAttributes(array &$children)
	{
		$guarded = with(new Menu)->getGuarded();

		foreach ($children as &$child)
		{
			$child = array_except($child, $guarded);

			$this->recursivelyStripAttributes($child['children']);
		}
	}

}
