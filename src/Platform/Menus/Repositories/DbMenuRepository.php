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
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Validator;

class DbMenuRepository implements MenuRepositoryInterface {

	/**
	 * The Eloquent content model.
	 *
	 * @var string
	 */
	protected $model;

	/**
	 * Holds the form validation rules.
	 *
	 * @var array
	 */
	protected $rules = array(
		'name' => 'required',
		'slug' => 'required|unique:menus,slug',
	);

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
		return $this->createModel()->allRoot();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findAll()
	{
		return $this->createModel()->findAll();
	}

	/**
	 * {@inheritDoc}
	 */
	public function find($id)
	{
		return $this->createModel()->orWhere('slug', $id)->orWhere('id', $id)->first();
	}

	/**
	 * Return all the menu slugs.
	 *
	 * @return array
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
	public function validForCreation(array $data)
	{
		return $this->validateMenu($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForUpdate($id, array $data)
	{
		$model = $this->find($id);

		$this->rules['slug'] = "required|unique:menus,slug,{$model->slug},slug";

		return $this->validateMenu($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(array $data)
	{
		$model = new $this->model(array(
			'name' => $data['name'],
			'slug' => $data['slug'],
		));

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

		foreach ($data as $key => $value)
		{
			if ($key === 'children')
			{
				$model->mapTree($value);
			}
			else
			{
				$model->{$key} = $value;
			}
		}

		$model->save();

		return $model;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete($id)
	{
		$model = $this->find($id);

		return $model->deleteWithChildren();
	}

	/**
	 * Validates a content.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Support\MessageBag
	 */
	protected function validateMenu($data)
	{
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
