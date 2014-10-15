<?php namespace Platform\Menus\Repositories;
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

use Cartalyst\Support\Traits;
use Illuminate\Container\Container;
use Lang;

class MenuRepository implements MenuRepositoryInterface {

	use Traits\EventTrait, Traits\RepositoryTrait, Traits\ValidatorTrait;

	/**
	 * The container instance.
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $app;

	/**
	 * The Illuminate Cache manager instance.
	 *
	 * @var \Illuminate\Cache\CacheManager
	 */
	protected $cache;

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
		$this->app = $app;

		$this->setDispatcher($app['events']);

		$this->setValidator($app['platform.menus.validator']);

		$this->model = get_class($this->app['Platform\Content\Models\Content']);
	}

	/**
	 * {@inheritDoc}
	 */
	public function grid()
	{
		$menus = $this->findAllRoot();

		foreach ($menus as &$menu)
		{
			$count = $menu->getChildrenCount();

			$menu->items_count = Lang::choice('platform/menus::table.items', $count, compact('count'));
		}

		return $menus;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findAll()
	{
		return $this->getCache()->rememberForever('platform.menu.all', function()
		{
			return $this
				->createModel()
				->findAll();
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function findAllRoot()
	{
		return $this->getCache()->rememberForever('platform.menu.all.root', function()
		{
			return $this
				->createModel()
				->allRoot();
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function find($id)
	{
		return $this->getCache()->rememberForever("platform.menu.{$id}", function() use ($id)
		{
			return $this
				->createModel()
				->orWhere('slug', $id)
				->orWhere('id', (int) $id)
				->first();
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function findRoot($id)
	{
		return $this->getCache()->rememberForever("platform.menu.root.{$id}", function() use ($id)
		{
			return $this->createModel()
				->orWhere('slug', $id)
				->orWhere('id', (int) $id)
				->where($this->createModel()->getReservedAttributeName('left'), 1)
				->first();
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function findWhere($column, $value)
	{
		return $this->getCache()->remember("platform.menu.where.{$column}.{$value}", 24 * 60, function() use ($column, $value)
		{
			return $this
				->createModel()
				->where($column, $value)
				->first();
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function slugs()
	{
		return array_map(function($child)
		{
			return $child['slug'];
		}, $this->findAll());
	}

	/**
	 * {@inheritDoc}
	 */
	public function getTypes()
	{
		return app('platform.menus.manager')->getTypes();
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
	public function validForUpdate($id, array $data)
	{
		$bindings = [ 'slug' => $this->find($id)->slug ];

		return $this->validator->on('update')->bind($bindings)->validate($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(array $input)
	{
		die;
		// // Create a new menu
		// $menu = $this->createModel();

		// // Fire the 'platform.menu.creating' event
		// $data = $this->fireEvent('platform.menu.creating', [ $input ])[0];

		// // Validate the submitted data
		// $messages = $this->validForCreation($data);

		// // Check if the validation returned any errors
		// if ($messages->isEmpty())
		// {
		// 	// Save the menu
		// 	$menu->fill($data)->save();

		// 	$menu->makeRoot();

		// 	if ($children = $data['children'])
		// 	{
		// 		$menu->mapTree($children);
		// 	}

		// 	// Fire the 'platform.menu.created' event
		// 	$this->fireEvent('platform.menu.created', $menu);
		// }

		// return [ $messages, $menu ];


		$menu = new $this->model([
			'name' => $data['name'],
			'slug' => $data['slug'],
		]);

		$menu->makeRoot();

		if ($children = $data['children'])
		{
			$menu->mapTree($children);
		}

		$this->dispatcher->fire('platform.menu.created', $menu);

		return $menu;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update($id, array $input)
	{
		die;
		// Get the menu object
		$menu = $this->find($id);

		// Fire the 'platform.menu.updating' event
		$data = $this->fireEvent('platform.menu.updating', [ $menu, $input ])[0];

		// Validate the submitted data
		$messages = $this->validForUpdate($id, $data);

		// Check if the validation returned any errors
		if ($messages->isEmpty())
		{
			foreach (array_except($data, ['children']) as $key => $value)
			{
				$menu->{$key} = $value;
			}

			// Update the menu
			$menu->save();

			$menu->mapTree(array_get($data, 'children', []));

			// Fire the 'platform.menu.updated' event
			$this->fireEvent('platform.menu.updated', $menu);
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
	public function delete($id)
	{
		// Check if the menu exists
		if ($menu = $this->find($id))
		{
			// Fire the 'platform.menu.deleted' event
			$this->fireEvent('platform.menu.deleted', $menu);

			// Delete the menu
			$menu->deleteWithChildren();

			return true;
		}

		return false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getCache()
	{
		if ( ! $this->cache)
		{
			$this->cache = $this->app['cache'];
		}

		return $this->cache;
	}

}
