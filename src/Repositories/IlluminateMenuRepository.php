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

use Cartalyst\Support\Traits\EventTrait;
use Cartalyst\Support\Traits\RepositoryTrait;
use Cartalyst\Support\Traits\ValidatorTrait;
use Illuminate\Cache\CacheManager;
use Illuminate\Events\Dispatcher;
use Lang;

class IlluminateMenuRepository implements MenuRepositoryInterface {

	use EventTrait, RepositoryTrait, ValidatorTrait;

	/**
	 * The Eloquent menu model.
	 *
	 * @var string
	 */
	protected $model;

	/**
	 * The Illuminate Cache manager instance.
	 *
	 * @var \Illuminate\Cache\CacheManager
	 */
	protected $cache;

	/**
	 * Constructor.
	 *
	 * @param  string  $model
	 * @param  \Illuminate\Events\Dispatcher  $dispatcher
	 * @param  \Illuminate\Cache\CacheManager  $cache
	 * @return void
	 */
	public function __construct($model, Dispatcher $dispatcher, CacheManager $cache)
	{
		$this->model = $model;

		$this->dispatcher = $dispatcher;

		$this->cache = $cache;
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
		return $this->cache->rememberForever('platform.menu.all', function()
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
		return $this->cache->rememberForever('platform.menu.all.root', function()
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
		return $this->cache->rememberForever("platform.menu.{$id}", function() use ($id)
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
		return $this->cache->rememberForever("platform.menu.root.{$id}", function() use ($id)
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
		return $this->cache->remember("platform.menu.where.{$column}.{$value}", 24 * 60, function() use ($column, $value)
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
		$model = $this->model;

		return $model::getTypes();
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForCreation(array $data)
	{
		return $this->validator
			->on('create')
			->validate($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForUpdate($id, array $data)
	{
		$menu = $this->find($id);

		return $this->validator
			->on('update')
			->bind(['slug' => $menu->slug])
			->validate($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(array $data)
	{
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
	public function update($id, array $data)
	{
		$menu = $this->find($id);

		foreach (array_except($data, ['children']) as $key => $value)
		{
			$menu->{$key} = $value;
		}

		$menu->save();

		$menu->mapTree(array_get($data, 'children', []));

		$this->dispatcher->fire('platform.menu.updated', $menu);

		return $menu;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete($id)
	{
		if ($menu = $this->find($id))
		{
			$this->dispatcher->fire('platform.menu.deleted', $menu);

			$menu->deleteWithChildren();

			return true;
		}

		return false;
	}

	/**
	 * Create a new instance of the model.
	 *
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function createModel()
	{
		$class = '\\'.ltrim($this->model, '\\');

		return new $class;
	}

}
