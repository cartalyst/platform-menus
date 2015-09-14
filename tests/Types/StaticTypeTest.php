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
 * @version    3.1.2
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Menus\Tests\Types;

use Mockery as m;
use Cartalyst\Testing\IlluminateTestCase;
use Platform\Menus\Types\StaticType;

class StaticTypeTest extends IlluminateTestCase
{
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        // Repository
        $this->type = new StaticType($this->app);
    }

    /** @test */
    public function it_has_static_identifier()
    {
        $this->assertEquals('static', $this->type->getIdentifier());
    }
}
