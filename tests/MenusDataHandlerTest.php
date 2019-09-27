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
 * @version    8.0.1
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2019, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Menus\Tests;

use Platform\Menus\Handlers\DataHandler;
use Cartalyst\Testing\IlluminateTestCase;

class MenusDataHandlerTest extends IlluminateTestCase
{
    /**
     * Setup.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = new DataHandler();
    }

    /** @test */
    public function it_can_prepare_data()
    {
        $data = [
            'menu-name' => 'Foo',
            'menu-slug' => 'foo',
            'menu-tree' => '[{"id":1,"children":[{"id":2,"children":[{"id":"foo-foo"}]}]}]',
            'children'  => [
                [
                    'name'    => 'Foo',
                    'slug'    => 'foo-foo',
                    'enabled' => '1',
                    'parent'  => '0',
                    'type'    => 'static',
                    'secure'  => '',
                    'static'  => [
                        'uri' => '',
                    ],
                    'page' => [
                        'page_id' => '1',
                    ],
                    'visibility' => 'always',
                    'class'      => 'fa fa-user',
                    'target'     => 'self',
                    'regex'      => '',
                ],
            ],
        ];

        $expected = [
            'name'     => 'Foo',
            'slug'     => 'foo',
            'children' => [
                [
                    'name'       => 'Foo Main',
                    'slug'       => 'foo-main',
                    'enabled'    => 1,
                    'type'       => 'static',
                    'secure'     => null,
                    'visibility' => 'always',
                    'roles'      => [],
                    'class'      => null,
                    'target'     => null,
                    'regex'      => null,
                    'id'         => 1,
                    'children'   => [
                        [
                            'name'       => 'Foo Sub',
                            'slug'       => 'foo-sub',
                            'enabled'    => 1,
                            'type'       => 'static',
                            'secure'     => null,
                            'visibility' => 'always',
                            'roles'      => [],
                            'class'      => null,
                            'target'     => null,
                            'regex'      => null,
                            'id'         => 2,
                            'children'   => [
                                [
                                    'name'       => 'Foo',
                                    'slug'       => 'foo',
                                    'enabled'    => 1,
                                    'type'       => 'static',
                                    'secure'     => null,
                                    'visibility' => 'always',
                                    'roles'      => [],
                                    'class'      => null,
                                    'target'     => null,
                                    'regex'      => null,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->app['request']->shouldReceive('input')
            ->with('children.1')
            ->once()
            ->andReturn([
                'name'    => 'Foo Main',
                'slug'    => 'foo-main',
                'enabled' => 1,
            ])
        ;

        $this->app['request']->shouldReceive('input')
            ->with('children.2')
            ->once()
            ->andReturn([
                'name'    => 'Foo Sub',
                'slug'    => 'foo-sub',
                'enabled' => 1,
            ])
        ;

        $this->app['request']->shouldReceive('input')
            ->with('children.foo-foo')
            ->once()
            ->andReturn([
                'name'    => 'Foo',
                'slug'    => 'foo',
                'enabled' => 1,
            ])
        ;

        $this->assertSame($expected, $this->handler->prepare($data));
    }
}
