<?php namespace Platform\Menus\Repositories;
/**
 * Part of the Platform package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Platform
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Events\Dispatcher;
use Lang;
use Validator;

class DbMenuRepository implements MenuRepositoryInterface {

	/**
	 * The Eloquent menu model.
	 *
	 * @var string
	 */
	protected $model;

	/**
	 * The event dispatcher instance.
	 *
	 * @var \Illuminate\Events\Dispatcher
	 */
	protected $dispatcher;

	/**
	 * Holds the form validation rules.
	 *
	 * @var array
	 */
	protected $rules = [
		'name' => 'required',
		'slug' => 'required|unique:menus',
	];

	/**
	 * Constructor.
	 *
	 * @param  string  $model
	 * @param  \Illuminate\Events\Dispatcher  $dispatcher
	 * @return void
	 */
	public function __construct($model, Dispatcher $dispatcher)
	{
		$this->model = $model;

		$this->dispatcher = $dispatcher;
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
		return $this
			->createModel()
			->findAll();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findAllRoot()
	{
		return $this
			->createModel()
			->allRoot();
	}

	/**
	 * {@inheritDoc}
	 */
	public function find($id)
	{
		return $this
			->createModel()
			->orWhere('slug', $id)
			->orWhere('id', (int) $id)
			->first();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findRoot($id)
	{
		return $this->createModel()
			->orWhere('slug', $id)
			->orWhere('id', (int) $id)
			->where($this->createModel()->getReservedAttributeName('left'), 1)
			->first();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findWhere($column, $value)
	{
		return $this
			->createModel()
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
		$model = $this->model;

		return $model::getTypes();
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForCreation(array $data)
	{
		return $this->validateMenu($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForUpdate($id, array $data)
	{
		return $this->validateMenu($data, $id);
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
	 * Validates a menu.
	 *
	 * @param  array  $data
	 * @param  mixed  $id
	 * @return \Illuminate\Support\MessageBag
	 */
	protected function validateMenu($data, $id = null)
	{
		if($id)
		{
			$model = $this->find($id);

			$this->rules['slug'] .= ",slug,{$model->slug},slug";
		}

		$validator = Validator::make($data, $this->rules);

		$validator->passes();

		return $validator->errors();
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
