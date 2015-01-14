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
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Mockery as m;
use Platform\Menus\Models\Menu;
use Cartalyst\Testing\IlluminateTestCase;
use Platform\Menus\Controllers\Admin\MenusController;

class AdminMenusControllerTest extends IlluminateTestCase {

	/**
	 * {@inheritDoc}
	 */
	public function setUp()
	{
		parent::setUp();

		// Foundation Controller expectations
		$this->app['sentinel']->shouldReceive('getUser');
		$this->app['view']->shouldReceive('share');

		// Menus repository
		$this->menus = m::mock('Platform\Menus\Repositories\MenuRepositoryInterface');

		// Controller
		$this->controller = new MenusController($this->menus);
	}

	/** @test */
	public function index_route()
	{
		$this->app['view']->shouldReceive('make')
			->atLeast()
			->once();

		$this->controller->index();
	}

	/** @test */
	public function create_route()
	{
		$this->app['view']->shouldReceive('make')
			->atLeast()
			->once();

		$this->menus->shouldReceive('getPreparedMenu')
			->once()
			->andReturn(['menu' => [], 'roles' => [], 'types' => [], 'children' => [], 'persistedSlugs' => []]);

		$this->controller->create();
	}

	/** @test */
	public function edit_route()
	{
		$this->app['view']->shouldReceive('make')
			->atLeast()
			->once();

		$this->menus->shouldReceive('getPreparedMenu')
			->once()
			->andReturn(['menu' => [], 'roles' => [], 'types' => [], 'children' => [], 'persistedSlugs' => []]);

		$this->controller->edit(1);
	}

	/** @test */
	public function edit_non_existing()
	{
		$this->app['alerts']->shouldReceive('error')
			->once();

		$this->menus->shouldReceive('getPreparedMenu')
			->once();

		$this->trans()->redirect('route');

		$this->controller->edit(1);
	}

	/** @test */
	public function datagrid()
	{
		$this->app['datagrid']->shouldReceive('make')
			->once();

		$this->menus->shouldReceive('grid')
			->once();

		$this->controller->grid();
	}

	/** @test */
	public function store()
	{
		$this->app['alerts']->shouldReceive('success')
			->once();

		$this->trans();

		$menuData = [
			'slug' => 'foo',
		];

		$this->app['request']->shouldReceive('all')
			->once()
			->andReturn($menuData);

		$this->menus->shouldReceive('store')
			->once()
			->with(null, $menuData)
			->andReturn([$message = m::mock('Illuminate\Support\MessageBag'), $model = m::mock('Platform\Menus\Models\Menu')]);

		$message->shouldReceive('isEmpty')
			->once()
			->andReturn(true);

		$this->redirect('route');

		$this->controller->store();
	}

	/** @test */
	public function update_route()
	{
		$this->app['alerts']->shouldReceive('success')
			->once();

		$this->trans();

		$menuData = [
			'slug' => 'foo',
		];

		$this->app['request']->shouldReceive('all')
			->once()
			->andReturn($menuData);

		$this->menus->shouldReceive('store')
			->once()
			->with(1, $menuData)
			->andReturn([$message = m::mock('Illuminate\Support\MessageBag'), $model = m::mock('Platform\Menus\Models\Menu')]);

		$message->shouldReceive('isEmpty')
			->once()
			->andReturn(true);

		$this->redirect('route');

		$this->controller->update(1, $menuData);
	}

	/** @test */
	public function update_invalid_route()
	{
		$this->app['alerts']->shouldReceive('error')
			->once();

		$menuData = [
			'slug' => 'foo',
		];

		$this->app['request']->shouldReceive('all')
			->once()
			->andReturn($menuData);

		$this->menus->shouldReceive('store')
			->once()
			->with(1, $menuData)
			->andReturn([$message = m::mock('Illuminate\Support\MessageBag'), $model = m::mock('Platform\Menus\Models\Menu')]);

		$message->shouldReceive('isEmpty')
			->once()
			->andReturn(false);

		$this->redirect('back')->redirect('withInput');

		$this->controller->update(1, $menuData);
	}

	/** @test */
	public function delete_route()
	{
		$this->app['alerts']->shouldReceive('success')
			->once();

		$this->trans();

		$this->menus->shouldReceive('delete')
			->once()
			->andReturn($model = m::mock('Platform\Menus\Models\Menu'));

		$this->redirect('route');

		$this->controller->delete(1);
	}

	/** @test */
	public function delete_not_existing_route()
	{
		$this->app['alerts']->shouldReceive('error')
			->once();

		$this->trans();

		$this->menus->shouldReceive('delete')
			->once();

		$this->redirect('route');

		$this->controller->delete(1);
	}

	/** @test */
	public function execute_action()
	{
		$this->app['request']->shouldReceive('input')
			->once()
			->with('action')
			->andReturn('delete');

		$this->app['request']->shouldReceive('input')
			->once()
			->with('rows', [])
			->andReturn([1]);

		$this->menus->shouldReceive('delete')
			->once()
			->with(1);

		$this->controller->executeAction();
	}

	/** @test */
	public function execute_non_existing_action()
	{
		$this->app['request']->shouldReceive('input')
			->once()
			->with('action')
			->andReturn('foo');

		$this->controller->executeAction();
	}

}
