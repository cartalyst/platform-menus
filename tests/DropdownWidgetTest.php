<?php

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
 * @version    4.0.2
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Menus\Tests;

use Mockery as m;
use Platform\Menus\Widgets\Dropdown;
use Cartalyst\Testing\IlluminateTestCase;

class DropdownWidgetTest extends IlluminateTestCase
{
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
    public function it_can_show_a_menu()
    {
        $widget = new Dropdown($this->menu);

        $menu = m::mock('Platform\Menus\Models\Menu');

        $menu->shouldReceive('findChildren')
            ->with(1)
            ->once()
            ->andReturn([]);

        $this->menu->shouldReceive('find')
            ->with('foo')
            ->once()
            ->andReturn($menu);

        $this->app['view']->shouldReceive('make')
            ->with('platform/menus::widgets/dropdown', ['items' => [], 'attributes' => [], 'options' => []], [])
            ->once();

        $widget->show('foo', 1);
    }

    /** @test */
    public function it_can_render_html_with_root_menus()
    {
        $widget = new Dropdown($this->menu);

        $menu = m::mock('Platform\Menus\Models\Menu');

        $child = m::mock('Platform\Menus\Models\Menu');

        $menu->shouldReceive('getChildren')
            ->once();

        $child->shouldReceive('getChildren')
            ->once();

        $menu->shouldReceive('setAttribute');

        $child->shouldReceive('setAttribute');

        $menu->shouldReceive('getAttribute')
            ->with('id')
            ->once()
            ->andReturn(1);

        $child->shouldReceive('getAttribute')
            ->with('id')
            ->once()
            ->andReturn(2);

        $menu->shouldReceive('getAttribute')
            ->with('depth')
            ->once()
            ->andReturn(1);

        $child->shouldReceive('getAttribute')
            ->with('depth')
            ->once()
            ->andReturn(1);

        $menu->shouldReceive('getAttribute')
            ->with('children')
            ->once()
            ->andReturn([$child]);

        $child->shouldReceive('getAttribute')
            ->with('children')
            ->once()
            ->andReturn([]);

        $this->app['view']->shouldReceive('make')
            ->with('platform/menus::widgets/dropdown', ['items' => [$menu], 'attributes' => [], 'options' => []], [])
            ->once();

        $this->menu->shouldReceive('allRoot')->once()
            ->andReturn([$menu]);

        $widget->root();
    }
}
