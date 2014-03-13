<?php namespace Platform\Menus\Repositories;
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
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

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
	 * Holds the form validation rules.
	 *
	 * @var array
	 */
	protected $rules = [
		'name' => 'required',
		'slug' => 'required|unique:menus',
	];

	/**
	 * Start it up.
	 *
	 * @param  string  $model
	 * @return void
	 */
	public function __construct($model)
	{
		$this->model = $model;
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
		$model = new $this->model([
			'name' => $data['name'],
			'slug' => $data['slug'],
		]);

		$model->makeRoot();

		if ($children = $data['children'])
		{
			$model->mapTree($children);
		}

		return $model;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update($id, array $data)
	{
		$model = $this->find($id);

		foreach (array_except($data, ['children']) as $key => $value)
		{
			$model->{$key} = $value;
		}

		$model->save();

		$model->mapTree(array_get($data, 'children', []));

		return $model;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete($id)
	{
		if ($model = $this->find($id))
		{
			$model->deleteWithChildren();

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
