<?php namespace Platform\Menus\Tests;
/**
 * Part of the Platform Menu extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Platform Menus extension
 * @version    1.0.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Mockery as m;
use Platform\Menus\Widgets\Nav;
use Cartalyst\Testing\IlluminateTestCase;

class NavWidgetTest extends IlluminateTestCase {

	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();

		// Menu repository
		$this->menu = m::mock('Platform\Menus\Repositories\MenuRepositoryInterface');
	}

	/** @test */
	public function it_can_return_without_childs()
	{
		$this->app['url']->shouldReceive('to')
			->once()
			->andReturn($this->app['url']);

		$this->app['url']->shouldReceive('current')
			->once()
			->andReturn('admin/foo');

		$widget = new Nav($this->app['sentinel'], $this->menu);

		$this->menu->shouldReceive('find')
			->with('foo')
			->once();

		$this->app['view']->shouldReceive('make')
			->with('platform/menus::widgets/nav', ['children' => [], 'cssClass' => ''], [])
			->once();

		$widget->show('foo', 1);
	}

	/** @test */
	public function it_returns_null_on_exception()
	{
		$this->app['url']->shouldReceive('to')
			->once()
			->andReturn($this->app['url']);

		$this->app['url']->shouldReceive('current')
			->once()
			->andReturn('admin/foo');

		$widget = new Nav($this->app['sentinel'], $this->menu);

		$this->assertNull($widget->show('foo', 1));
	}

	/** @test */
	public function it_can_show_a_menu_with_regex()
	{
		$this->app['url']->shouldReceive('to')
			->once()
			->andReturn($this->app['url']);

		$this->app['url']->shouldReceive('current')
			->once()
			->andReturn('admin/foo');

		$widget = new Nav($this->app['sentinel'], $this->menu);

		$menu = m::mock('Platform\Menus\Models\Menu');
		$child = m::mock('Platform\Menus\Models\Menu');

		$menu->shouldReceive('findDisplayableChildren')
			->with(['always', 'logged_out'], [], 1)
			->once()
			->andReturn([ $menu ]);

		$menu->shouldReceive('getChildren')
			->once()
			->andReturn([]);

		$child->shouldReceive('getChildren')
			->once()
			->andReturn([]);

		$menu->shouldReceive('setAttribute');

		$child->shouldReceive('setAttribute');

		$menu->shouldReceive('getAttribute')
			->with('children')
			->andReturn([ $child ]);

		$child->shouldReceive('getAttribute')
			->with('children')
			->andReturn([]);

		$menu->shouldReceive('getAttribute')
			->with('path')
			->andReturn('foo');

		$menu->shouldReceive('getAttribute')
			->with('regex')
			->andReturn('/:admin\/foo/i');

		$menu->shouldReceive('getAttribute');

		$child->shouldReceive('getAttribute');

		$menu->shouldReceive('getUrl')
			->once();

		$child->shouldReceive('getUrl')
			->once();

		$this->app['sentinel']->shouldReceive('check')
			->once();

		$this->menu->shouldReceive('find')
			->with('foo')
			->once()
			->andReturn($menu);

		$this->app['view']->shouldReceive('make')
			->with('platform/menus::widgets/nav', ['children' => [ $menu ], 'cssClass' => ''], [])
			->once();

		$widget->show('foo', 1);
	}

	/** @test */
	public function it_can_show_a_menu_without_regex()
	{
		$this->app['url']->shouldReceive('to')
			->once()
			->andReturn($this->app['url']);

		$this->app['url']->shouldReceive('current')
			->once()
			->andReturn('admin/foo');

		$widget = new Nav($this->app['sentinel'], $this->menu);

		$menu = m::mock('Platform\Menus\Models\Menu');

		$menu->shouldReceive('findDisplayableChildren')
			->with(['always', 'logged_out'], [], 1)
			->once()
			->andReturn([ $menu ]);

		$menu->shouldReceive('getChildren')
			->once()
			->andReturn([]);

		$menu->shouldReceive('setAttribute');

		$menu->shouldReceive('getAttribute')
			->with('children')
			->andReturn([]);

		$menu->shouldReceive('getAttribute')
			->with('uri')
			->andReturn('admin/foo');

		$menu->shouldReceive('getAttribute');

		$menu->shouldReceive('getUrl')
			->once();

		$this->app['sentinel']->shouldReceive('check')
			->once();

		$this->menu->shouldReceive('find')
			->with('foo')
			->once()
			->andReturn($menu);

		$this->app['view']->shouldReceive('make')
			->with('platform/menus::widgets/nav', ['children' => [$menu], 'cssClass' => ''], [])
			->once();

		$widget->show('foo', 1);
	}

}
