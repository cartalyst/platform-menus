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
 * @version    3.1.3
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Platform\Menus\Validator;

use Cartalyst\Support\Validator;

class MenusValidator extends Validator implements MenusValidatorInterface
{
    /**
     * {@inheritDoc}
     */
    protected $rules = [
        'name' => 'required',
        'slug' => 'required|unique:menus',
    ];

    /**
     * {@inheritDoc}
     */
    public function onUpdate()
    {
        $this->rules['slug'] .= ",slug,{slug},slug";
    }
}
