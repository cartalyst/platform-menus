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
 * @version    5.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Extensions\ExtensionInterface;
use Cartalyst\Settings\Repository as Settings;
use Illuminate\Contracts\Foundation\Application;
use Cartalyst\Permissions\Container as Permissions;
use Illuminate\Contracts\Routing\Registrar as Router;

return [

    /*
    |--------------------------------------------------------------------------
    | Slug
    |--------------------------------------------------------------------------
    |
    | This is the extension unique identifier and should not be
    | changed as it will be recognized as a new extension.
    |
    | Note:
    |
    |   Ideally this should match the folder structure within the
    |   extensions folder, however this is completely optional.
    |
    */

    'slug' => 'platform/menus',

    /*
    |--------------------------------------------------------------------------
    | Name
    |--------------------------------------------------------------------------
    |
    | This is the extension name, used mainly for presentational purposes.
    |
    */

    'name' => 'Menus',

    /*
    |--------------------------------------------------------------------------
    | Description
    |--------------------------------------------------------------------------
    |
    | A brief sentence describing what the extension does.
    |
    */

    'description' => 'Manage all the menus throughout your website.',

    /*
    |--------------------------------------------------------------------------
    | Version
    |--------------------------------------------------------------------------
    |
    | This is the extension version and it should be set as a string
    | so it can be used with the version_compare() function.
    |
    */

    'version' => '5.0.0',

    /*
    |--------------------------------------------------------------------------
    | Author
    |--------------------------------------------------------------------------
    |
    | Because everybody deserves credit for their work, right?
    |
    */

    'author' => 'Cartalyst LLC',

    /*
    |--------------------------------------------------------------------------
    | Requirements
    |--------------------------------------------------------------------------
    |
    | Define here all the extensions that this extension depends on to work.
    |
    | Note:
    |
    |   This is used in conjunction with Composer, so you should put the
    |   exact same dependencies on the extension composer.json require
    |   array, so that they get resolved automatically by Composer.
    |
    |   However you can use without Composer, at which point you will
    |   have to ensure that the required extensions are available!
    |
    */

    'requires' => [

        'platform/access',

    ],

    /*
    |--------------------------------------------------------------------------
    | Service Providers
    |--------------------------------------------------------------------------
    |
    | Define here your extension service providers. They will be dynamically
    | registered without having to include them in config/app.php file.
    |
    */

    'providers' => [

        Platform\Menus\Providers\MenusServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    |
    | Closure that is called when the extension is started. You can register
    | any custom routing logic here.
    |
    | The closure parameters are:
    |
    |   object \Illuminate\Contracts\Routing\Registrar  $router
    |	object \Cartalyst\Extensions\ExtensionInterface  $extension
    |	object \Illuminate\Contracts\Foundation\Application  $app
    |
    */

    'routes' => function (Router $router, ExtensionInterface $extension, Application $app) {
        if (! $app->routesAreCached()) {
            $router->group([
                'prefix' => admin_uri().'/menus', 'namespace' => 'Platform\Menus\Controllers\Admin'
            ], function (Router $router) {
                $router->get('/', 'MenusController@index')->name('admin.menus.all');
                $router->post('/', 'MenusController@executeAction')->name('admin.menus.all');

                $router->get('grid', 'MenusController@grid')->name('admin.menus.grid');

                $router->get('create', 'MenusController@create')->name('admin.menu.create');
                $router->post('create', 'MenusController@store')->name('admin.menu.create');

                $router->get('{id}', 'MenusController@edit')->name('admin.menu.edit');
                $router->post('{id}', 'MenusController@update')->name('admin.menu.edit');
                $router->delete('{id}', 'MenusController@delete')->name('admin.menu.delete');
            });
        }
    },

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    |
    | Register here all the permissions that this extension has. These will
    | be shown in the user management area to build a graphical interface
    | where permissions can be selected to allow or deny user access.
    |
    | For detailed instructions on how to register the permissions, please
    | refer to the following url https://cartalyst.com/manual/permissions
    |
    | The closure parameters are:
    |
    |   object \Cartalyst\Permissions\Container  $permissions
    |	object \Illuminate\Contracts\Foundation\Application  $app
    |
    */

    'permissions' => function (Permissions $permissions, Application $app) {
        $permissions->group('menus', function ($g) {
            $g->name = trans('platform/menus::common.title');

            $g->permission('menus.index', function ($p) {
                $p->label = trans('platform/menus::permissions.index');

                $p->controller('Platform\Menus\Controllers\Admin\MenusController', 'index, grid');
            });

            $g->permission('menus.create', function ($p) {
                $p->label = trans('platform/menus::permissions.create');

                $p->controller('Platform\Menus\Controllers\Admin\MenusController', 'create, store');
            });

            $g->permission('menus.edit', function ($p) {
                $p->label = trans('platform/menus::permissions.edit');

                $p->controller('Platform\Menus\Controllers\Admin\MenusController', 'edit, update');
            });

            $g->permission('menus.delete', function ($p) {
                $p->label = trans('platform/menus::permissions.delete');

                $p->controller('Platform\Menus\Controllers\Admin\MenusController', 'delete');
            });
        });
    },

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | Register here all the settings that this extension has.
    |
    | For detailed instructions on how to register the settings, please
    | refer to the following url https://cartalyst.com/manual/settings
    |
    | The closure parameters are:
    |
    |   object \Cartalyst\Settings\Repository  $settings
    |	object \Illuminate\Contracts\Foundation\Application  $app
    |
    */

    'settings' => function (Settings $settings, Application $app) {

    },

    /*
    |--------------------------------------------------------------------------
    | Menus
    |--------------------------------------------------------------------------
    |
    | You may specify the default various menu hierarchy for your extension.
    |
    | You can provide a recursive array of menu children and their children.
    |
    | These will be created upon installation, synchronized upon upgrading
    | and removed upon uninstallation.
    |
    | Menu children are automatically put at the end of the menu for
    | extensions installed through the Operations extension.
    |
    | The default order (for extensions installed initially) can be
    | found by editing the file "config/platform.php".
    |
    */

    'menus' => [

        'admin' => [

            [
                'slug'  => 'admin-menus',
                'name'  => 'Menus',
                'class' => 'fa fa-th-list',
                'uri'   => 'menus',
                'regex' => '/:admin\/menus/i',
            ],

        ],

        'system' => [

            [
                'slug'   => 'system-preview',
                'name'   => 'Preview',
                'class'  => 'fa fa-laptop',
                'uri'    => '/',
                'target' => 'blank',
            ],

            [
                'slug'  => 'system-settings',
                'name'  => 'Settings',
                'class' => 'fa fa-sliders',
                'uri'   => ':admin/settings',
                'regex' => '/:admin\/settings/i',
            ],

        ],

        'main' => [

            [
                'slug'  => 'main-about',
                'name'  => 'About',
                'class' => 'fa fa-info',
                'uri'   => 'about',
            ],

            [
                'slug'     => 'main-help',
                'name'     => 'Help',
                'class'    => 'fa fa-life-ring',
                'children' => [

                    [
                        'slug'   => 'main-help-support',
                        'name'   => 'Support',
                        'class'  => 'fa fa-bug',
                        'uri'    => 'https://cartalyst.com/support',
                        'target' => 'blank',
                    ],

                    [
                        'slug'  => 'main-help-docs',
                        'name'  => 'Documentation',
                        'class' => 'fa fa-graduation-cap',
                        'uri'   => 'https://cartalyst.com/manual/platform',
                        'target' => 'blank',
                    ],

                    [
                        'slug'  => 'main-help-license',
                        'name'  => 'License',
                        'class' => 'fa fa-book',
                        'uri'   => 'https://cartalyst.com/license',
                        'target' => 'blank',
                    ],

                ],

            ],

        ],

        'account' => [

            [
                'slug'     => 'account-menu',
                'name'     => 'Account',
                'class'    => 'fa fa-user',
                'regex'    => '/profile/i',
                'children' => [

                    [
                        'slug'       => 'account-admin',
                        'name'       => 'Administrator',
                        'class'      => 'fa fa-gears',
                        'uri'        => ':admin/',
                        'visibility' => 'admin',
                    ],

                    [
                        'slug'       => 'account-profile',
                        'name'       => 'Profile',
                        'class'      => 'fa fa-gear',
                        'uri'        => 'profile',
                        'visibility' => 'logged_in',
                    ],

                    [
                        'slug'       => 'account-login',
                        'name'       => 'Sign In',
                        'class'      => 'fa fa-sign-in',
                        'uri'        => 'login',
                        'visibility' => 'logged_out',
                    ],

                    [
                        'slug'       => 'account-logout',
                        'name'       => 'Logout',
                        'class'      => 'fa fa-sign-out',
                        'uri'        => 'logout',
                        'visibility' => 'logged_in',
                    ],

                    [
                        'slug'       => 'account-register',
                        'name'       => 'Register',
                        'class'      => 'fa fa-edit',
                        'uri'        => 'register',
                        'visibility' => 'logged_out',
                    ],

                ],

            ],

        ],

    ],

];
