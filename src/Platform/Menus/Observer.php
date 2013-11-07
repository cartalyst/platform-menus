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
use Platform\Menus\Models\Menu;

class Observer {

	/**
	 * Register a creating model event with the dispatcher.
	 *
	 * @param  \Closure|string  $callback
	 * @return void
	 */
	public function creating($model)
	{
		echo 'Being created';
		die;
	}

	/**
	 * Register an updating model event with the dispatcher.
	 *
	 * @param  \Closure|string  $callback
	 * @return void
	 */
	public function updating($model)
	{
		if ($type = $model->getType())
		{
			if ($typeData = array_get($model->getAttributes(), 'type_data'))
			{
				$model->setTypeData($typeData);

				$model->unsetAttribute('type_data');
			}

			$type->afterSave($model);
		}
	}

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
		if ( ! $menus = $this->extractMenus($extension)) return;

		foreach ($menus as $slug => $children)
		{
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

			$query = with(new Menu)
			    ->newQuery()
			    ->where('extension', '=', $extension->getSlug())
			    ->where('slug', 'like', "{$slug}-");

			if (count($slugs))
			{
				$query->whereNotIn('slug', $slugs);
			}

			foreach ($query->get() as $child)
			{
				$child->refresh();
				$child->delete();
			}

			// Firstly, we'll purge the existing children (and any descendents)
			// from the children array and put them in the existing array.
			$method = camel_case($slug).'Menu';
			with($menu = Menu::$method())->findChildren();
			$tree     = $menu->toArray();
			$existing = $tree['children'];
			$this->recursivelyPurgeExisting($children, $existing);

			$tree = array_merge($existing, $children);
			$this->recursivelyStripAttributes($tree);

			// Because we have just taken our existing hierarchy
			// and added to it, we can save on the overhead of
			// orphaning or deleting children as there'll never
			// be any here. So, we'll just call this method as
			// a speed improvement.
			$menu->mapTreeAndKeep($tree);
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
		if ( ! $menus = $this->extractMenus($extension)) return;

		foreach ($menus as $slug => $children)
		{
			// Build up an array of all the slugs present in the children array
			$slugs = array();
			if (is_array($children) and ! empty($children))
			{
				array_walk_recursive($children, function($value, $key) use (&$slugs)
				{
					if ($key == 'slug') $slugs[] = $value;
				});
			}

			$query = with(new Menu)
			    ->newQuery()
			    ->where('extension', '=', $extension->getSlug());

			if (count($slugs) > 0)
			{
				$query->orWhereIn('slug', $slugs);
			}

			// Refresh our nodes so they're not affected by deletions and
			// remove them.
			foreach ($query->get() as $child)
			{
				$child->refresh();
				$child->delete();
			}
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
		$children = with(new Menu)
		    ->newQuery()
		    ->where('extension', '=', $extension->getSlug())
		    ->get();

		foreach ($children as $child)
		{
			$child->enabled = true;
			$child->save();
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
		$children = with(new Menu)
		    ->newQuery()
		    ->where('extension', '=', $extension->getSlug())
		    ->get();

		foreach ($children as $child)
		{
			$child->enabled = false;
			$child->save();
		}
	}

	/**
	 * Extract all valid menus from an extension.php file,
	 * returnin an array where the key is a menu slug and
	 * the value is an array of children.
	 *
	 * @param  Cartalyst\Extensions\Extension  $extension
	 * @return array
	 */
	protected function extractMenus(Extension $extension)
	{
		$menus = $extension->menus;

		// If the attribute physically doesn't exist, we'll just
		// skip. They may have chosen to create the items progamatically
		// so we won't want to remove everything they've done.
		if ( ! is_array($menus))
		{
			return;
		}

		$valid = array();

		// If there's no children, we'll observe an uninstall
		// event for the child. This'll remove any children
		// in the database.
		foreach ($menus as $slug => $menu)
		{
			if ( ! is_array($menus)) continue;
			$valid[$slug] = $menu;
		}

		return $valid;
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

			if ( ! isset($child['type'])) $child['type']             = 'static';
			if ( ! isset($child['visibility'])) $child['visibility'] = 'always';
			if ( ! isset($child['children'])) $child['children']     = array();

			if ( ! is_array($child['children']))
			{
				throw new \InvalidArgumentException("Menu child [{$child['slug']}] for Extension [{$extension->getSlug()}] has a children property that is not an array.");
			}

			$this->recursivelyPrepareChildren($extension, $child['children']);
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
