<?php namespace Platform\Menus\Tests;
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

use Mockery as m;
use Cartalyst\Testing\IlluminateTestCase;

class MenuRepositoryTest extends IlluminateTestCase {

	/**
	 * {@inheritDoc}
	 */
	public function setUp()
	{
		parent::setUp();

		// Additional Bindings
		$this->app['sentinel.menus']              = m::mock('Cartalyst\Sentinel\Menus\MenuRepositoryInterface');
		$this->app['platform.roles']              = m::mock('Platform\Roles\Repositories\RoleRepositoryInterface');
		$this->app['platform.menus.handler.data'] = m::mock('Platform\Menus\Handlers\DataHandlerInterface');
		$this->app['platform.menus.manager']      = m::mock('Platform\Menus\Repositories\ManagerRepository');
		$this->app['platform.menus.validator']    = m::mock('Cartalyst\Support\Validator');

		// Repository
		$this->repository = m::mock('Platform\Menus\Repositories\MenuRepository[createModel]', [$this->app]);
	}

	/** @test */
	public function it_can_generate_the_grid()
	{
		$this->repository->shouldReceive('createModel')
			->once()
			->andReturn($model = m::mock('Platform\Menus\Models\Menu'));

		$model->shouldReceive('allRoot')
			->once()
			->andReturn([]);

		$collection = m::mock('Illuminate\Database\Eloquent\Collection');

		$this->app['cache']->shouldReceive('rememberForever')
			->once()
			->with('platform.menus.all.root', m::on(function($callback)
			{
				$callback();
				return true;
			}))->andReturn($collection);

		$collection->shouldReceive('getIterator')
			->once()
			->andReturn($iterator = m::mock('Iterator'));

		$iterator->shouldReceive('rewind')
			->once();

		$iterator->shouldReceive('valid')
			->once()
			->andReturn(true);

		$iterator->shouldReceive('valid')
			->andReturn(false);

		$iterator->shouldReceive('current')
			->once()
			->andReturn($menu = m::mock('Platform\Menus\Models\Menu'));

		$menu->shouldReceive('getChildrenCount')
			->andReturn(2);

		$menu->shouldReceive('setAttribute')
			->with('items_count', 2)
			->once();

		$iterator->shouldReceive('next')
			->andReturn(false);

		$this->app['translator']
			->shouldReceive('transChoice')
			->with('platform/menus::model.general.items', 2, ['count' => 2], 'messages', '')
			->once()
			->andReturn(2);

		$this->repository->grid();
	}

	/** @test */
	public function it_can_find_all()
	{
		$this->repository->shouldReceive('createModel')
			->once()
			->andReturn($model = m::mock('Platform\Menus\Models\Menu'));

		$model->shouldReceive('findAll')
			->once()
			->andReturn($collection = m::mock('Illuminate\Database\Eloquent\Collection'));

		$this->app['cache']->shouldReceive('rememberForever')
			->once()
			->with('platform.menus.all', m::on(function($callback)
			{
				$callback();
				return true;
			}))->andReturn($collection);

		$menus = $this->repository->findAll();

		$this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $menus);
	}

	/** @test */
	public function it_can_find_all_root()
	{
		$this->repository->shouldReceive('createModel')
			->once()
			->andReturn($model = m::mock('Platform\Menus\Models\Menu'));

		$model->shouldReceive('allRoot')
			->once()
			->andReturn([]);

		$collection = m::mock('Illuminate\Database\Eloquent\Collection');

		$this->app['cache']->shouldReceive('rememberForever')
			->once()
			->with('platform.menus.all.root', m::on(function($callback)
			{
				$callback();
				return true;
			}))->andReturn($collection);

		$menus = $this->repository->findAllRoot();

		$this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $menus);
	}

	/** @test */
	public function it_can_find_records_by_id()
	{
		$model = $this->shouldReceiveFind();

		$this->repository->find(1);
	}

	/** @test */
	public function it_can_find_records_by_slug()
	{
		$model = m::mock('Platform\Menus\Models\Menu');

		$this->repository->shouldReceive('createModel')
			->once()
			->andReturn($model);

		$this->app['cache']->shouldReceive('rememberForever')
			->once()
			->with('platform.menu.slug.foo', m::on(function($callback)
			{
				$callback();
				return true;
			}));

		$model->shouldReceive('whereSlug')
			->once()
			->andReturn($model);

		$model->shouldReceive('first')
			->once()
			->andReturn($model);

		$this->repository->find('foo');
	}

	/** @test */
	public function it_can_find_root_records_by_id()
	{
		$model = m::mock('Platform\Menus\Models\Menu');

		$model->shouldReceive('getReservedAttributeName');

		$model->shouldReceive('find')
			->once();

		$model->shouldReceive('where')
			->once()
			->andReturn($model);

		$this->repository->shouldReceive('createModel')
			->once()
			->andReturn($model);

		$this->app['cache']->shouldReceive('rememberForever')
			->once()
			->with('platform.menu.root.1', m::on(function($callback)
			{
				$callback();
				return true;
			}));

		$this->repository->findRoot(1);
	}

	/** @test */
	public function it_can_find_root_records_by_slug()
	{
		$model = m::mock('Platform\Menus\Models\Menu');

		$model->shouldReceive('getReservedAttributeName');

		$model->shouldReceive('where')
			->once()
			->andReturn($model);

		$this->repository->shouldReceive('createModel')
			->once()
			->andReturn($model);

		$this->app['cache']->shouldReceive('rememberForever')
			->once()
			->with('platform.menu.root.slug.foo', m::on(function($callback)
			{
				$callback();
				return true;
			}));

		$model->shouldReceive('whereSlug')
			->once()
			->andReturn($model);

		$model->shouldReceive('first')
			->once()
			->andReturn($model);

		$this->repository->findRoot('foo');
	}

	/** @test */
	public function it_can_find_records_where()
	{
		$model = m::mock('Platform\Menus\Models\Menu');

		$this->repository->shouldReceive('createModel')
			->once()
			->andReturn($model);

		$model->shouldReceive('where')
			->with('foo', 'bar')
			->once()
			->andReturn($model);

		$model->shouldReceive('first')
			->once();

		$this->repository->findWhere('foo', 'bar');
	}

	/** @test */
	public function it_can_find_all_records_where()
	{
		$model = m::mock('Platform\Menus\Models\Menu');

		$this->repository->shouldReceive('createModel')
			->once()
			->andReturn($model);

		$model->shouldReceive('where')
			->with('foo', 'bar')
			->once()
			->andReturn($model);

		$model->shouldReceive('get')
			->once();

		$this->repository->findAllWhere('foo', 'bar');
	}

	/** @test */
	public function it_can_retrieve_prepared_menus()
	{
		$this->app['platform.roles']
			->shouldReceive('findAll')
			->once();

		$model = $this->shouldReceiveCreateModel(2);

		$model->shouldReceive('findAll')
			->once()
			->andReturn([]);

		$this->app['cache']->shouldReceive('rememberForever')
			->once()
			->with('platform.menus.all', m::on(function($callback)
			{
				$callback();
				return true;
			}))
			->andReturn([]);

		$this->app['platform.menus.manager']->shouldReceive('getTypes')
			->once();

		$preparedMenu = $this->repository->getPreparedMenu(null);

		$this->assertArrayHasKey('menu', $preparedMenu);
		$this->assertArrayHasKey('persistedSlugs', $preparedMenu);
		$this->assertArrayHasKey('roles', $preparedMenu);
		$this->assertArrayHasKey('types', $preparedMenu);
		$this->assertArrayHasKey('children', $preparedMenu);
	}

	/** @test */
	public function it_create_a_new_model_if_the_passed_id_cannot_be_found()
	{
		$this->app['platform.roles']
			->shouldReceive('findAll')
			->once();

		$model = $this->shouldReceiveCreateModel(2);

		$model->shouldReceive('findAll')
			->once()
			->andReturn([]);

		$this->app['cache']->shouldReceive('rememberForever')
			->once()
			->with('platform.menus.all', m::on(function($callback)
			{
				$callback();
				return true;
			}))
			->andReturn([]);

		$this->app['platform.menus.manager']->shouldReceive('getTypes')
			->once();

		$preparedMenu = $this->repository->getPreparedMenu(null);

		$this->assertArrayHasKey('menu', $preparedMenu);
		$this->assertArrayHasKey('persistedSlugs', $preparedMenu);
		$this->assertArrayHasKey('roles', $preparedMenu);
		$this->assertArrayHasKey('types', $preparedMenu);
		$this->assertArrayHasKey('children', $preparedMenu);
	}

	/** @test */
	public function it_returns_false_if_no_menu_is_found()
	{
		$model = $this->shouldReceiveCreateModel();

		$model->shouldReceive('getReservedAttributeName');

		$model->shouldReceive('where')
			->once()
			->andReturn($model);

		$model->shouldReceive('find')
			->once();

		$this->app['cache']->shouldReceive('rememberForever')
			->once()
			->with('platform.menu.root.2', m::on(function($callback)
			{
				$callback();
				return true;
			}));

		$preparedMenu = $this->repository->getPreparedMenu(2);

		$this->assertFalse($preparedMenu);
	}

	/** @test */
	public function it_can_validate_for_creation()
	{
		$data = ['slug' => 'foo'];

		$this->app['platform.menus.validator']->shouldReceive('on')
			->with('create')
			->once()
			->andReturn($this->app['platform.menus.validator']);

		$this->app['platform.menus.validator']->shouldReceive('validate')
			->once()
			->andReturn(true);

		$this->repository->setValidator($this->app['platform.menus.validator']);

		$this->assertTrue($this->repository->validForCreation($data));
	}

	/** @test */
	public function it_can_validate_for_update()
	{
		$data = ['slug' => 'foo'];

		$model = m::mock('Platform\Menus\Models\Menu');

		$this->app['platform.menus.validator']->shouldReceive('on')
			->with('update')
			->once()
			->andReturn($this->app['platform.menus.validator']);

		$this->app['platform.menus.validator']->shouldReceive('bind')
			->with($data)
			->once()
			->andReturn($this->app['platform.menus.validator']);

		$this->app['platform.menus.validator']->shouldReceive('validate')
			->once()
			->andReturn(true);

		$model->shouldReceive('getAttribute')
			->once()
			->with('slug')
			->andReturn('foo');

		$this->assertTrue($this->repository->validForUpdate($model, $data));
	}

	/** @test */
	public function it_can_store()
	{
		$data = ['slug' => 'foo', 'children' => ['foo']];

		$this->shouldReceiveCreate($data);

		$this->repository->store(null, $data);
	}

	/** @test */
	public function it_can_create()
	{
		$data = ['slug' => 'foo', 'children' => ['foo']];

		$this->shouldReceiveCreate($data);

		list($messages, $menu) = $this->repository->create($data);

		$this->assertInstanceOf('Platform\Menus\Models\Menu', $menu);
	}

	/** @test */
	public function it_will_stop_creating_if_event_returns_false()
	{
		$data = ['slug' => 'foo'];

		$this->app['events']->shouldReceive('fire')
			->with('platform.menu.creating', [ $data ])
			->once()
			->andReturn(false);

		$this->repository->shouldReceive('createModel')
			->once()
			->andReturn($model = m::mock('Platform\Menus\Models\Menu'));

		list($messages, $menu) = $this->repository->create($data);

		$this->assertNull($menu);
	}

	/** @test */
	public function it_will_stop_updating_if_event_returns_false()
	{
		$data = ['slug' => 'foo'];

		$model = $this->shouldReceiveFind();

		$this->app['events']->shouldReceive('fire')
			->with('platform.menu.updating', [ $model, $data ])
			->once()
			->andReturn(false);

		list($messages, $menu) = $this->repository->update(1, $data);

		$this->assertNull($menu);
	}

	/** @test */
	public function it_can_update_existing_records()
	{
		$data = ['slug' => 'foo', 'children' => ['foo']];

		$this->shouldReceiveUpdate($data);

		list($messages, $menu) = $this->repository->update(1, $data);

		$this->assertInstanceOf('Platform\Menus\Models\Menu', $menu);
	}

	/** @test */
	public function it_can_delete_existing_records()
	{
		$model = $this->shouldReceiveFind();

		$this->app['events']->shouldReceive('fire')
			->with('platform.menu.deleted', [ $model ])
			->once();

		$model->shouldReceive('deleteWithChildren')
			->once();

		$this->assertTrue($this->repository->delete(1));
	}

	/** @test */
	public function it_will_return_false_when_trying_to_delete_non_existing_records()
	{
		$model = $this->shouldReceiveFind(null, false);

		$this->assertFalse($this->repository->delete(1));
	}

	/** @test */
	public function it_can_enable_attributes()
	{
		$data = ['enabled' => 1];

		$model = $this->shouldReceiveFind();

		$this->app['platform.menus.validator']->shouldReceive('bypass')
			->once();

		$this->app['events']->shouldReceive('fire')
			->with('platform.menu.updating', [ $model, $data ])
			->once();

		$this->app['platform.menus.handler.data']->shouldReceive('prepare')
			->once()
			->andReturn($data);

		$this->app['platform.menus.validator']->shouldReceive('on')
			->with('update')
			->once()
			->andReturn($this->app['platform.menus.validator']);

		$this->app['platform.menus.validator']->shouldReceive('bind')
			->once()
			->andReturn($this->app['platform.menus.validator']);

		$this->app['platform.menus.validator']->shouldReceive('validate')
			->once()
			->andReturn($messages = m::mock('Illuminate\Support\MessageBag'));

		$messages->shouldReceive('isEmpty')
			->once()
			->andReturn(true);

		$model->shouldReceive('getAttribute')
			->once()
			->with('slug')
			->andReturn('foo');

		$model->shouldReceive('setAttribute');

		$model->shouldReceive('save')
			->once();

		$this->app['events']->shouldReceive('fire')
			->with('platform.menu.updated', [ $model ])
			->once();

		list($messages, $menu) = $this->repository->enable(1);

		$this->assertInstanceOf('Platform\Menus\Models\Menu', $menu);
	}

	/** @test */
	public function it_can_disable_attributes()
	{
		$data = ['enabled' => 0];

		$model = $this->shouldReceiveFind();

		$this->app['platform.menus.validator']->shouldReceive('bypass')
			->once();

		$this->app['events']->shouldReceive('fire')
			->with('platform.menu.updating', [ $model, $data ])
			->once();

		$this->app['platform.menus.handler.data']->shouldReceive('prepare')
			->once()
			->andReturn($data);

		$this->app['platform.menus.validator']->shouldReceive('on')
			->with('update')
			->once()
			->andReturn($this->app['platform.menus.validator']);

		$this->app['platform.menus.validator']->shouldReceive('bind')
			->once()
			->andReturn($this->app['platform.menus.validator']);

		$this->app['platform.menus.validator']->shouldReceive('validate')
			->once()
			->andReturn($messages = m::mock('Illuminate\Support\MessageBag'));

		$messages->shouldReceive('isEmpty')
			->once()
			->andReturn(true);

		$model->shouldReceive('getAttribute')
			->once()
			->with('slug')
			->andReturn('foo');

		$model->shouldReceive('setAttribute');

		$model->shouldReceive('save')
			->once();

		$this->app['events']->shouldReceive('fire')
			->with('platform.menu.updated', [ $model ])
			->once();

		list($messages, $menu) = $this->repository->disable(1);

		$this->assertInstanceOf('Platform\Menus\Models\Menu', $menu);
	}

	/**
	 * Repository should receive createModel.
	 *
	 * @return mixed
	 */
	protected function shouldReceiveCreateModel($times = 1)
	{
		$model = m::mock('Platform\Menus\Models\Menu');

		$this->repository->shouldReceive('createModel')
			->times($times)
			->andReturn($model);

		return $model;
	}

	/*
	 * Find method expectation.
	 *
	 * @param  mixed  $model
	 * @param  bool  $returnModel
	 * @return mixed
	 */
	protected function shouldReceiveFind($model = null, $returnModel = true)
	{
		$model = $model ?: m::mock('Platform\Menus\Models\Menu');

		$this->repository->shouldReceive('createModel')
			->once()
			->andReturn($model);

		$cacheExpectation = $this->app['cache']->shouldReceive('rememberForever')
			->once()
			->with('platform.menu.1', m::on(function($callback)
			{
				$callback();
				return true;
			}));

		$modelExpectation = $model->shouldReceive('find')
			->once();

		if ($returnModel)
		{
			$modelExpectation->andReturn($model);
			$cacheExpectation->andReturn($model);
		}

		return $model;
	}

	/**
	 * Menu creation expectations.
	 *
	 * @param  arary  $data
	 * @return void
	 */
	protected function shouldReceiveCreate($data)
	{
		$this->app['events']->shouldReceive('fire')
			->with('platform.menu.creating', [ $data ])
			->once();

		$this->app['events']->shouldReceive('fire')
			->with('platform.menu.created', m::any())
			->once();

		$this->app['platform.menus.handler.data']->shouldReceive('prepare')
			->once()
			->andReturn($data);

		$this->app['platform.menus.validator']->shouldReceive('on')
			->with('create')
			->once()
			->andReturn($this->app['platform.menus.validator']);

		$this->app['platform.menus.validator']->shouldReceive('validate')
			->once()
			->with($data)
			->andReturn($messages = m::mock('Illuminate\Support\MessageBag'));

		$messages->shouldReceive('isEmpty')
			->once()
			->andReturn(true);

		$this->repository->shouldReceive('createModel')
			->once()
			->andReturn($model = m::mock('Platform\Menus\Models\Menu'));

		$model->shouldReceive('mapTree')
			->once();

		$model->shouldReceive('setAttribute');

		$model->shouldReceive('makeRoot')
			->once();
	}

	/**
	 * Menu update expectations.
	 *
	 * @param  arary  $data
	 * @return void
	 */
	protected function shouldReceiveUpdate($data)
	{
		$model = $this->shouldReceiveFind();

		$this->app['events']->shouldReceive('fire')
			->with('platform.menu.updating', [ $model, $data ])
			->once();

		$this->app['platform.menus.handler.data']->shouldReceive('prepare')
			->once()
			->andReturn($data);

		$model->shouldReceive('getAttribute')
			->once()
			->with('slug')
			->andReturn('foo');

		$this->app['events']->shouldReceive('fire')
			->with('platform.menu.updated', [ $model ])
			->once();

		$this->app['platform.menus.validator']->shouldReceive('on')
			->with('update')
			->once()
			->andReturn($this->app['platform.menus.validator']);

		$this->app['platform.menus.validator']->shouldReceive('bind')
			->with(array_except($data, 'children'))
			->once()
			->andReturn($this->app['platform.menus.validator']);

		$model->shouldReceive('mapTree')
			->once();

		$this->app['platform.menus.validator']->shouldReceive('validate')
			->once()
			->andReturn($messages = m::mock('Illuminate\Support\MessageBag'));

		$messages->shouldReceive('isEmpty')
			->once()
			->andReturn(true);

		$model->shouldReceive('setAttribute');

		$model->shouldReceive('save')
			->once();
	}

}
