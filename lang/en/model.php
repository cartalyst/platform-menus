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

return [

    'general' => [

        'create' => [
            'legend'      => 'Create New Link',
            'description' => 'Add a new navigation item.',
        ],

        'update' => [
            'legend'      => 'Edit Item',
            'description' => 'Update an existing navigation item.',
        ],

        'name'      => 'Menu Name',
        'name_help' => 'Type your menu item name.',

        'slug'      => 'Slug',
        'slug_help' => 'Single word, no spaces and no special words. Dashes are allowed.',

        'name_item'      => 'Link Name',
        'name_item_help' => 'Type your menu item name.',

        'slug_item'      => 'Slug',
        'slug_item_help' => 'Single word, no spaces and no special words. Dashes are allowed.',

        'enabled'      => 'Status',
        'enabled_help' => 'What is this menu item status?',

        'parent'      => 'Parent',
        'parent_help' => 'Chose the parent that this item belongs to or leave the default option selected for it to be a "root" menu item of this menu.',

        'type'      => 'Type',
        'type_help' => 'Select the item url type.',

        'type_static' => 'Static',

        'uri'      => 'Uri',
        'uri_help' => 'Type in your menu item uri.',

        'secure'      => 'HTTPS',
        'secure_help' => 'Should this url be served over HTTPS?',

        'visibility'      => 'Visibility',
        'visibility_help' => 'When should this menu item be seen?',

        'visibilities' => [
            'always'     => 'Show Always',
            'logged_in'  => 'Logged In',
            'logged_out' => 'Logged Out',
            'admin'      => 'Admin Only',
        ],

        'roles'      => 'Roles',
        'roles_help' => 'What user roles should be able to see this menu item?',

        'class'      => 'Class',
        'class_help' => 'Class that will be assigned to the <li> element surrounding your menu item.',

        'target'      => 'Target',
        'target_help' => 'The target attribute specifies where to open the menu item.',

        'targets' => [
            'self'   => 'Same Window',
            'blank'  => 'New Window',
            'parent' => 'Parent Frame',
            'top'    => 'Top Frame (Main Document)',
        ],

        'regex'      => 'Regular Expression',
        'regex_help' => 'Regex pattern for advanced "selected" states.',

        'created_at' => 'Created At',

        'items_count' => '# of Items',
        'items'       => '{0} No items|{1} 1 item|[2,Inf] :count items',

        'item_details' => 'Details',
        'advanced_settings' => 'Advanced Settings',

    ],

];
