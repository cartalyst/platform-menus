<?php

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
 * @version    3.2.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Menus\Tests;

use Mockery as m;
use Cartalyst\Testing\IlluminateTestCase;
use Platform\Menus\Repositories\ManagerRepository;

class ManagerRepositoryTest extends IlluminateTestCase
{
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        // Repository
        $this->repository = new ManagerRepository();
    }

    /** @test */
    public function it_can_register_and_retrieve_types()
    {
        $type = m::mock('Platform\Menus\Types\TypeInterface');

        $type->shouldReceive('getIdentifier')
            ->once()
            ->andReturn('foo');

        $this->repository->registerType($type);

        $this->assertSame($type, array_get($this->repository->getTypes(), 'foo'));
    }

    /** @test */
    public function it_can_retrieve_type()
    {
        $type = m::mock('Platform\Menus\Types\TypeInterface');

        $type->shouldReceive('getIdentifier')
            ->once()
            ->andReturn('foo');

        $this->repository->registerType($type);

        $this->assertArrayHasKey('foo', $this->repository->getTypes());
        $this->assertSame($type, $this->repository->getType('foo'));
    }

    /** @test */
    public function it_returns_null_if_type_does_not_exist()
    {
        $this->assertNull($this->repository->getType('foo'));
    }
}
