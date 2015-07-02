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
 * @version    3.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Menus\Tests\Types;

use Mockery as m;
use Platform\Menus\Models\Menu;
use Platform\Menus\Types\AbstractType;
use Cartalyst\Testing\IlluminateTestCase;

class AbstractTypeTest extends IlluminateTestCase
{
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        // Repository
        $this->type = new FooType($this->app);
    }

    /** @test */
    public function it_can_retrieve_the_type_name()
    {
        $this->app['translator']->shouldReceive('trans')
            ->with('platform/menus::model.general.type_foo')
            ->once();

        $this->type->getName();
    }

    /** @test */
    public function it_can_retrieve_the_name_attribute()
    {
        $menu = m::mock('Platform\Menus\Models\Menu');

        $menu->shouldReceive('getAttribute')
            ->with('name')
            ->once();

        $this->type->getNameAttribute($menu);
    }

    /** @test */
    public function it_can_retrieve_the_url_for_the_child()
    {
        $menu = m::mock('Platform\Menus\Models\Menu');

        $menu->shouldReceive('getAttribute')
            ->with('uri')
            ->once()
            ->andReturn('bar');

        $menu->shouldReceive('getAttribute')
            ->with('secure')
            ->once()
            ->andReturn(false);

        $this->app['url']->shouldReceive('to')
            ->with('foo/bar', [], false);

        $this->type->getUrlAttribute($menu, ['before_uri' => 'foo']);
    }

    /** @test */
    public function it_can_retrieve_the_form_html()
    {
        $menu = m::mock('Platform\Menus\Models\Menu');

        $this->app['view']->shouldReceive('make')
            ->with('platform/menus::types/foo/form', ['child' => $menu])
            ->once();

        $this->type->getFormHtml($menu);
    }

    /** @test */
    public function it_can_retrieve_the_template_html()
    {
        $this->app['view']->shouldReceive('make')
            ->with('platform/menus::types/foo/template')
            ->once();

        $this->type->getTemplateHtml();
    }
}

class FooType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function getIdentifier()
    {
        return 'foo';
    }

    /**
     * {@inheritDoc}
     */
    public function afterSave(Menu $child)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function beforeDelete(Menu $child)
    {
    }
}
