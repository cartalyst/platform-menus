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
 * @version    3.1.2
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Menus\Tests;

use Mockery as m;
use Platform\Menus\Observer;
use Illuminate\Support\Collection;
use Cartalyst\Testing\IlluminateTestCase;

class MenuObserverTest extends IlluminateTestCase
{
    /**
     * Setup.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        // Additional IoC bindings
        $this->app['platform.menus'] = $this->menu = m::mock('Platform\Menus\Repositories\MenuRepositoryInterface');
        $this->app['sentinel.roles'] = m::mock('Cartalyst\Sentinel\Roles\RoleRepositoryInterface');

        // Menu repository
        $this->observer = new Observer($this->app);
    }

    /** @test */
    public function it_can_register_updating_event()
    {
        $menu = m::mock('Platform\Menus\Models\Menu');

        $menu->shouldReceive('getAttribute');

        $this->menu->shouldReceive('getManager')
            ->once()
            ->andReturn($manager = m::mock('Platform\Menus\Repositories\ManagerRepository'));

        $manager->shouldReceive('getType')
            ->once()
            ->andReturn($type = m::mock('Platform\Menus\Types\StaticType'));

        $type->shouldReceive('afterSave')
            ->with($menu)
            ->once();

        $this->observer->updating($menu);
    }

    /** @test */
    public function it_triggers_after_install_event()
    {
        $extension = m::mock('Cartalyst\Extensions\Extension');

        $menu = m::mock('Platform\Menus\Models\Menu');

        $menus = [

            'admin' => [

                [
                    'slug'  => 'admin-menus',
                    'name'  => 'Menus',
                    'uri'   => 'menus',
                    'roles' => ['admin', 'subscribers'],
                    'regex' => '/:admin\/menus/i',
                ],

            ],

        ];

        $extension->shouldReceive('getAttribute')
            ->with('menus')
            ->once()
            ->andReturn($menus);

        $extension->shouldReceive('getSlug')
            ->times(4)
            ->andReturn('foo/bar');

        $this->menu->shouldReceive('createModel')
            ->times(4)
            ->andReturn($menu);

        $this->menu->shouldReceive('findBySlug')
            ->with('admin')
            ->once();

        $this->menu->shouldReceive('create')
            ->with([
                'menu-name' => 'Admin',
                'menu-slug' => 'admin',
            ])
            ->once()
            ->andReturn([null, $menu]);

        $menu->shouldReceive('findChildren')
            ->once()
            ->andReturn([]);

        $menu->shouldReceive('getKey')
            ->once()
            ->andReturn('id');

        $menu->shouldReceive('whereMenu')
            ->once()
            ->andReturn($menu);

        $menu->shouldReceive('whereExtension')
            ->twice()
            ->andReturn($menu);

        $menu->shouldReceive('where')
            ->once()
            ->andReturn($menu);

        $menu->shouldReceive('whereNotIn')
            ->with('slug', ['admin-menus'])
            ->once()
            ->andReturn($menu);

        $menu->shouldReceive('get')
            ->twice()
            ->andReturn([$menu]);

        $menu->shouldReceive('refresh')
            ->once();

        $menu->shouldReceive('delete')
            ->once();

        $menu->shouldReceive('toArray')
            ->once()
            ->andReturn(['children' => []]);

        $menu->shouldReceive('getGuarded')
            ->twice()
            ->andReturn([
                'lft',
                'rgt',
                'menu',
                'depth',
                'created_at',
                'updated_at',
            ]);

        $this->app['sentinel.roles']->shouldReceive('createModel')
            ->twice()
            ->andReturn($role = m::mock('Platform\Roles\Models\Role'));

        $role->shouldReceive('lists')
            ->with('id', 'slug')
            ->twice()
            ->andReturn(new Collection([
                [
                    'id' => 1,
                    'slug' => 'admin',
                ],
                [
                    'id' => 2,
                    'slug' => 'subscribers',
                ],
            ]));

        $menu->shouldReceive('mapTreeAndKeep')
            ->once();

        $menu->shouldReceive('setAttribute');

        $menu->shouldReceive('save')
            ->once();

        $this->observer->afterInstall($extension);
    }

    /** @test */
    public function it_triggers_after_uninstall_event()
    {
        $extension = m::mock('Cartalyst\Extensions\Extension');

        $menu = m::mock('Platform\Menus\Models\Menu');

        $extension->shouldReceive('getAttribute')
            ->with('menus')
            ->once()
            ->andReturn([['slug' => 'foo', 'roles' => ['admin', 'subscribers']]]);

        $this->menu->shouldReceive('createModel')
            ->once()
            ->andReturn($menu);

        $extension->shouldReceive('getSlug')
            ->once()
            ->andReturn('foo/bar');

        $menu->shouldReceive('orWhereIn')
            ->once()
            ->with('slug', ['foo'])
            ->andReturn($menu);

        $menu->shouldReceive('whereExtension')
            ->once()
            ->andReturn($menu);

        $menu->shouldReceive('get')
            ->once()
            ->andReturn([$menu]);

        $menu->shouldReceive('refresh')
            ->once();

        $menu->shouldReceive('delete')
            ->once();

        $this->observer->afterUninstall($extension);
    }

    /** @test */
    public function it_triggers_after_enable_event()
    {
        $extension = m::mock('Cartalyst\Extensions\Extension');

        $menu = m::mock('Platform\Menus\Models\Menu');

        $extension->shouldReceive('getSlug')
            ->once()
            ->andReturn('foo/bar');

        $this->menu->shouldReceive('createModel')
            ->once()
            ->andReturn($menu);

        $menu->shouldReceive('whereExtension')
            ->once()
            ->andReturn($menu);

        $menu->shouldReceive('get')
            ->once()
            ->andReturn([$menu]);

        $menu->shouldReceive('setAttribute')
            ->with('enabled', true)
            ->once();

        $menu->shouldReceive('save')
            ->once();

        $this->observer->afterEnable($extension);
    }

    /** @test */
    public function it_triggers_after_disable_event()
    {
        $extension = m::mock('Cartalyst\Extensions\Extension');

        $menu = m::mock('Platform\Menus\Models\Menu');

        $extension->shouldReceive('getSlug')
            ->once()
            ->andReturn('foo/bar');

        $this->menu->shouldReceive('createModel')
            ->once()
            ->andReturn($menu);

        $menu->shouldReceive('whereExtension')
            ->once()
            ->andReturn($menu);

        $menu->shouldReceive('get')
            ->once()
            ->andReturn([$menu]);

        $menu->shouldReceive('setAttribute')
            ->with('enabled', false)
            ->once();

        $menu->shouldReceive('save')
            ->once();

        $this->observer->afterDisable($extension);
    }
}
