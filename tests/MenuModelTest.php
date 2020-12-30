<?php

/*
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
 * @version    10.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2020, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Menus\Tests;

use Mockery as m;
use Illuminate\Support\Arr;
use Platform\Menus\Models\Menu;
use Cartalyst\Testing\IlluminateTestCase;

class MenuModelTest extends IlluminateTestCase
{
    /**
     * Close mockery.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->addToAssertionCount(1);

        m::close();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Additional IoC bindings
        $this->app['platform.menus.manager'] = m::mock('Platform\Menus\Repositories\ManagerRepository');

        $this->menu       = new Menu();
        $this->menu->type = 'static';
    }

    /** @test */
    public function it_has_an_enabled_mutator_and_accessor()
    {
        $enabled = 0;

        $this->menu->exists = true;

        $this->menu->enabled = $enabled;

        $this->assertFalse($this->menu->enabled);
    }

    /** @test */
    public function it_has_a_secure_mutator_and_accessor()
    {
        $secure = 1;

        $this->assertNull($this->menu->secure);

        $this->menu->secure = $secure;

        $this->assertTrue($this->menu->secure);

        $this->menu->secure = null;

        $this->assertNull($this->menu->secure);

        $this->menu->secure = '';

        $this->assertNull($this->menu->secure);
    }

    /** @test */
    public function it_has_a_roles_mutator_and_accessor()
    {
        $roles = [
            1,
            2,
        ];

        $this->menu->roles = $roles;

        $this->assertSame(json_encode($roles), Arr::get($this->menu->getAttributes(), 'roles'));

        $this->assertSame($roles, $this->menu->roles);
    }

    /** @test */
    public function it_can_retrieve_the_menu_type()
    {
        $this->app['platform.menus.manager']->shouldReceive('getType')
            ->with('static')
            ->once()
            ->andReturn($type = m::mock('Platform\Menus\Types\StaticType'))
        ;

        $this->assertSame($type, $this->menu->getType());
    }

    /** @test */
    public function it_can_find_displayable_children()
    {
        $this->shouldFindDisplayableChildren($this->menu);

        $this->menu->findDisplayableChildren(['admin'], [1, 2]);
    }

    /** @test */
    public function it_can_dynamically_forward_methods_to_the_manager()
    {
        // getIdentifier method from the manager
        $this->app['platform.menus.manager']->shouldReceive('getType')
            ->with('static')
            ->once()
            ->andReturn(m::mock('Platform\Menus\Types\StaticType[getTypes]', [$this->app]))
        ;

        $this->menu->getIdentifier();

        // getUrlAttribute method from the manager
        $this->app['platform.menus.manager']->shouldReceive('getType')
            ->with('static')
            ->once()
            ->andReturn(m::mock('Platform\Menus\Types\StaticType[getTypes]', [$this->app]))
        ;

        $this->app['Illuminate\Contracts\Routing\UrlGenerator']->shouldReceive('to')
            ->once()
        ;

        $this->menu->getUrl();
    }

    /**
     * @test
     */
    public function it_can_dynamically_forward_methods_to_the_parent()
    {
        $this->app['platform.menus.manager']->shouldReceive('getType')
            ->with('static')
            ->once()
            ->andReturn(m::mock('Platform\Menus\Types\StaticType[getTypes]', [$this->app]))
        ;

        $this->menu->query()->shouldReceive('foo');

        $this->menu->foo();
    }

    /**
     * Adds a mock connection to the object.
     *
     * @param mixed $model
     *
     * @return void
     */
    protected function shouldFindDisplayableChildren($model)
    {
        // Resolver
        $resolver = m::mock('Illuminate\Database\ConnectionResolverInterface');

        $resolver->shouldReceive('connection')
            ->andReturn(m::mock('Illuminate\Database\Connection'))
        ;

        // Model
        $model->setConnectionResolver($resolver);

        // Connection
        $connection = $model->getConnection();

        $connection->shouldReceive('getPostProcessor')
            ->andReturn($processor = m::mock('Illuminate\Database\Query\Processors\Processor'))
        ;

        $connection->shouldReceive('getName')
            ->andReturn('mysql')
        ;

        $connection->shouldReceive('select');

        $connection->shouldReceive('table')
            ->twice()
            ->andReturn($builder = m::mock('Illuminate\Database\Eloquent\Builder'))
        ;

        $connection->shouldReceive('query')
            ->andReturn($query = m::mock('Illuminate\Database\Query\Builder'))
        ;

        $connection->shouldReceive('getQueryGrammar')
            ->andReturn($grammar = m::mock('Illuminate\Database\Query\Grammars\Grammar'))
        ;

        $query->shouldReceive('from');

        $query->shouldReceive('where');

        $query->shouldReceive('whereNotNull');

        $query->shouldReceive('get')
            ->andReturn($collection = m::mock('Illuminate\Database\Eloquent\Collection'))
        ;

        $query->shouldReceive('getConnection')
            ->andReturn($connection)
        ;

        $query->shouldReceive('whereIntegerInRaw')
            ->once()
        ;

        $collection->shouldReceive('all')
            ->andReturn([])
        ;

        // Grammar
        $grammar->shouldReceive('compileSelect');

        $grammar->shouldReceive('wrap')
            ->andReturn($grammar)
        ;

        $grammar->shouldReceive('getTablePrefix');

        // Builder
        $builder->shouldReceive('join')
            ->andReturn($builder)
        ;

        $builder->shouldReceive('whereNested')
            ->with(m::on(function ($callback) use ($builder) {
                $callback($builder);

                return true;
            }))
            ->andReturn($builder)
        ;

        $builder->shouldReceive('orWhere')
            ->andReturn($builder)
        ;

        $builder->shouldReceive('orWhereNull')
            ->andReturn($builder)
        ;

        $builder->shouldReceive('where')
            ->andReturn($builder)
        ;

        $builder->shouldReceive('mergeBindings')
            ->once()
            ->andReturn($builder)
        ;

        $builder->shouldReceive('whereIn')
            ->once()
            ->andReturn($builder)
        ;

        $builder->shouldReceive('orderBy')
            ->once()
            ->andReturn($builder)
        ;

        $builder->shouldReceive('groupBy')
            ->once()
            ->andReturn($builder)
        ;

        $builder->shouldReceive('get')
            ->once()
            ->andReturn([$model])
        ;
    }
}
