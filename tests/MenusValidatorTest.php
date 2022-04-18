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
 * @version    11.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2022, Cartalyst LLC
 * @link       https://cartalyst.com
 */

namespace Platform\Menus\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Platform\Menus\Validator\MenusValidator;

class MenusValidatorTest extends TestCase
{
    /**
     * Close mockery.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * Setup.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->validator = new MenusValidator(m::mock('Illuminate\Validation\Factory'));
    }

    /** @test */
    public function it_can_validate()
    {
        $rules = [
            'name' => 'required',
            'slug' => 'required|unique:menus',
        ];

        $this->assertSame($rules, $this->validator->getRules());

        $this->validator->onUpdate();

        $rules['slug'] .= ',slug,{slug},slug';

        $this->assertSame($rules, $this->validator->getRules());
    }
}
