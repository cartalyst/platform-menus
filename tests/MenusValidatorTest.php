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
 * @version    6.0.4
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Menus\Tests;

use Mockery as m;
use PHPUnit_Framework_TestCase;
use Platform\Menus\Validator\MenusValidator;

class MenusValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Close mockery.
     *
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Setup.
     *
     * @return void
     */
    public function setUp()
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

        $this->assertEquals($rules, $this->validator->getRules());

        $this->validator->onUpdate();

        $rules['slug'] .= ',slug,{slug},slug';

        $this->assertEquals($rules, $this->validator->getRules());
    }
}
