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
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Mockery as m;
use Cartalyst\Testing\IlluminateTestCase;
use Platform\Menus\Handlers\EventHandler;

class MenuEventHandlerTest extends IlluminateTestCase {

	/**
	 * {@inheritDoc}
	 */
	public function setUp()
	{
		parent::setUp();

		// Handler
		$this->handler = new EventHandler($this->app);
	}

	/** @test */
	public function test_subscribe()
	{
		$class = get_class($this->handler);

		$this->app['events']->shouldReceive('listen')
			->once()
			->with('platform.menu.creating', $class.'@creating');

		$this->app['events']->shouldReceive('listen')
			->once()
			->with('platform.menu.created', $class.'@created');

		$this->app['events']->shouldReceive('listen')
			->once()
			->with('platform.menu.updating', $class.'@updating');

		$this->app['events']->shouldReceive('listen')
			->once()
			->with('platform.menu.updated', $class.'@updated');

		$this->app['events']->shouldReceive('listen')
			->once()
			->with('platform.menu.deleted', $class.'@deleted');

		$this->handler->subscribe($this->app['events']);
	}

	/** @test */
	public function test_created()
	{
		$menu = m::mock('Platform\Menus\Models\Menu');

		$this->shouldFlushCache($menu);

		$this->handler->created($menu, []);
	}

	/** @test */
	public function test_updated()
	{
		$menu = m::mock('Platform\Menus\Models\Menu');

		$this->shouldFlushCache($menu);

		$this->handler->updated($menu, []);
	}

	/** @test */
	public function test_deleted()
	{
		$menu = m::mock('Platform\Menus\Models\Menu');

		$this->shouldFlushCache($menu);

		$this->handler->deleted($menu);
	}

	/**
	 * Sets expected method calls for flushing cache.
	 *
	 * @param  \Platform\Content\Models\Content  $menu
	 * @return void
	 */
	protected function shouldFlushCache($menu)
	{
		$this->app['cache']->shouldReceive('forget')
			->once()
			->with("platform.menu.1");

		$this->app['cache']->shouldReceive('forget')
			->once()
			->with('platform.menu.root.1');

		$this->app['cache']->shouldReceive('forget')
			->once()
			->with('platform.menu.slug.foo');

		$this->app['cache']->shouldReceive('forget')
			->once()
			->with('platform.menu.root.slug.foo');

		$this->app['cache']->shouldReceive('forget')
			->once()
			->with('platform.menus.all');

		$this->app['cache']->shouldReceive('forget')
			->once()
			->with('platform.menus.all.root');

		$menu->shouldReceive('getAttribute')
			->twice()
			->with('id')
			->andReturn(1);

		$menu->shouldReceive('getAttribute')
			->twice()
			->with('slug')
			->andReturn('foo');
	}

}
