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
	 * The Data handler.
	 *
	 * @var \Platform\Menus\Handlers\DataHandlerInterface
	 */
	protected $data;

	/**
	 * The Eloquent menu model.
	 *
	 * @var string
	 */
	protected $model;

	/**
	 * The Sentinel instance.
	 *
	 * @var \Cartalyst\Sentinel\Sentinel
	 */
	protected $sentinel;

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

		$this->sentinel = $this->app['sentinel'];

		$this->setValidator($app['platform.menus.validator']);

		$this->data = $this->app['platform.menus.handler.data'];

		$this->model = get_class($this->app['Platform\Menus\Models\Menu']);
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

			$menu->items_count = trans_choice('platform/menus::table.items', $count, compact('count'));
		}

		return $menus;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findAll()
	{
		// return $this->createModel()->rememberForever('platform.menus.all')->findAll();
		return $this->getCache()->rememberForever('platform.menus.all', function()
		{
			return $this->createModel()->findAll();
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function findAllRoot()
	{
		// return $this->createModel()->rememberForever('platform.menus.all.root')->allRoot();
		return $this->getCache()->rememberForever('platform.menus.all.root', function()
		{
			return $this->createModel()->allRoot();
		});
	}

	/**
	 * {@inheritDoc}
	 */
	public function find($id)
	{
		$model = $this->createModel()->rememberForever('platform.menu.'.$id);

		if (is_numeric($id)) return $model->find($id);

		return $model->whereSlug($id)->first();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findRoot($id)
	{
		$model = $this->createModel();

		$menu = $model->rememberForever('platform.menu.root.'.$id)
					  ->where($model->getReservedAttributeName('left'), 1);

		if (is_numeric($id)) return $menu->find($id);

		return $menu->whereSlug($id)->first();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findWhere($column, $value)
	{
		return $this
				->createModel()
				->remember('platform.menu.where.'.$column.'.'.$value, 24 * 60)
				->where($column, $value)
				->first();
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
		// Create a new menu
		$menu = $this->createModel();

		// Fire the 'platform.menu.creating' event
		if ($this->fireEvent('platform.menu.creating') === false)
		{
			return false;
		}

		// Prepare the submitted data
		$data = $this->data->prepare($input);

		// Validate the submitted data
		$messages = $this->validForCreation($data);

		// Check if the validation returned any errors
		if ($messages->isEmpty())
		{
			// Update the menu
			foreach (array_except($data, ['children']) as $key => $value)
			{
				$menu->{$key} = $value;
			}

			// Set this new menu as root
			$menu->makeRoot();

			// Set this menu children
			$menu->mapTree(array_get($data, 'children', []));

			// Fire the 'platform.menu.created' event
			$this->fireEvent('platform.menu.created', $menu);
		}

		return [ $messages, $menu ];
	}

	/**
	 * {@inheritDoc}
	 */
	public function update($id, array $input)
	{
		// Get the menu object
		$menu = $this->find($id);

		// Fire the 'platform.menu.updating' event
		if ($this->fireEvent('platform.menu.updating', [ $menu ]) === false)
		{
			return false;
		}

		// Prepare the submitted data
		$data = $this->data->prepare($input);

		// Validate the submitted data
		$messages = $this->validForUpdate($id, array_except($data, 'children'));

		// Check if the validation returned any errors
		if ($messages->isEmpty())
		{
			// Update the menu
			foreach (array_except($data, ['children']) as $key => $value)
			{
				$menu->{$key} = $value;
			}
			$menu->save();

			// Set this menu children
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

	public function getPreparedMenu($id)
	{
		// Do we have a menu identifier?
		if (isset($id))
		{
			// Get the menu information
			if ( ! $menu = $this->findRoot($id)) return false;

			// Get this menu children
			$children = $menu->findChildren(0);
		}
		else
		{
			$menu = $this->createModel();
		}

		// Get the persisted slugs
		$persistedSlugs = $this->slugs();

		// Get a list of all the available roles
		$roles = $this->sentinel->getRoleRepository()->createModel()->all();

		// Get all the registered menu types
		$types = $this->getTypes();

		return compact('menu', 'persistedSlugs', 'roles', 'types', 'children');
	}

}
