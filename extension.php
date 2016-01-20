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

use Cartalyst\Extensions\ExtensionInterface;
use Cartalyst\Settings\Repository as Settings;
use Illuminate\Contracts\Foundation\Application;
use Cartalyst\Permissions\Container as Permissions;

return [

    /*
    |--------------------------------------------------------------------------
    | Name
    |--------------------------------------------------------------------------
    |
    | Your extension name (it's only required for presentational purposes).
    |
    */

    'name' => 'Menus',

    /*
    |--------------------------------------------------------------------------
    | Slug
    |--------------------------------------------------------------------------
    |
    | Your extension unique identifier and should not be changed as
    | it will be recognized as a whole new extension.
    |
    | Ideally, this should match the folder structure within the extensions
    | folder, but this is completely optional.
    |
    */

    'slug' => 'platform/menus',

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
    | Description
    |--------------------------------------------------------------------------
    |
    | One or two sentences describing what the extension do for
    | users to view when they are installing the extension.
    |
    */

    'description' => 'Manage all the menus throughout your website.',

    /*
    |--------------------------------------------------------------------------
    | Version
    |--------------------------------------------------------------------------
    |
    | Version should be a string that can be used with version_compare().
    |
    */

    'version' => '3.2.0',

    /*
    |--------------------------------------------------------------------------
    | Requirements
    |--------------------------------------------------------------------------
    |
    | List here all the extensions that this extension requires to work.
    |
    | This is used in conjunction with composer, so you should put the
    | same extension dependencies on your main composer.json require
    | key, so that they get resolved using composer, however you
    | can use without composer, at which point you'll have to
    | ensure that the required extensions are available.
    |
    */

    'require' => [

        'platform/access',

    ],

    /*
    |--------------------------------------------------------------------------
    | Autoload Logic
    |--------------------------------------------------------------------------
    |
    | You can define here your extension autoloading logic, it may either
    | be 'composer', 'platform' or a 'Closure'.
    |
    | If composer is defined, your composer.json file specifies the autoloading
    | logic.
    |
    | If platform is defined, your extension receives convetion autoloading
    | based on the Platform standards.
    |
    | If a Closure is defined, it should take two parameters as defined
    | bellow:
    |
    |	object \Composer\Autoload\ClassLoader  $loader
    |	object \Illuminate\Contracts\Foundation\Application  $app
    |
    | Supported: "composer", "platform", "Closure"
    |
    */

    'autoload' => 'composer',

    /*
    |--------------------------------------------------------------------------
    | Service Providers
    |--------------------------------------------------------------------------
    |
    | Define your extension service providers here. They will be dynamically
    | registered without having to include them in app/config/app.php.
    |
    */

    'providers' => [

        'Platform\Menus\Providers\MenusServiceProvider',

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
    |	object \Cartalyst\Extensions\ExtensionInterface  $extension
    |	object \Illuminate\Contracts\Foundation\Application  $app
    |
    */

    'routes' => function (ExtensionInterface $extension, Application $app) {
        if (! $app->routesAreCached()) {
            Route::group([
                'prefix'    => admin_uri().'/menus',
                'namespace' => 'Platform\Menus\Controllers\Admin'
            ], function () {
                Route::get('/', ['as' => 'admin.menus.all', 'uses' => 'MenusController@index']);
                Route::post('/', ['as' => 'admin.menus.all', 'uses' => 'MenusController@executeAction']);

                Route::get('grid', ['as' => 'admin.menus.grid', 'uses' => 'MenusController@grid']);

                Route::get('create', ['as' => 'admin.menu.create', 'uses' => 'MenusController@create']);
                Route::post('create', ['as' => 'admin.menu.create', 'uses' => 'MenusController@store']);

                Route::get('{id}', ['as' => 'admin.menu.edit', 'uses' => 'MenusController@edit']);
                Route::post('{id}', ['as' => 'admin.menu.edit', 'uses' => 'MenusController@update']);
                Route::delete('{id}', ['as' => 'admin.menu.delete', 'uses' => 'MenusController@delete']);
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
    | found by editing the file "app/config/platform.php".
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

    /*
    |--------------------------------------------------------------------------
    | Widgets
    |--------------------------------------------------------------------------
    |
    | Closure that is called when the extension is started. You can register
    | all your custom widgets here. Of course, Platform will guess the
    | widget class for you, this is just for custom widgets or if you
    | do not wish to make a new class for a very small widget.
    |
    */

    'widgets' => null,

];
