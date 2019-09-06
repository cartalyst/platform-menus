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
 * @version    8.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2019, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Menus\Tests\Types;

use Platform\Menus\Types\StaticType;
use Cartalyst\Testing\IlluminateTestCase;

class StaticTypeTest extends IlluminateTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Repository
        $this->type = new StaticType($this->app);
    }

    /** @test */
    public function it_has_static_identifier()
    {
        $this->assertSame('static', $this->type->getIdentifier());
    }
}
