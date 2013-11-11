<?php
/**
 * Part of the Platform application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Platform
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Extensions\ExtensionInterface;
use Cartalyst\Extensions\Extension;
use Illuminate\Foundation\Application;
use Platform\Menus\Models\Menu;

return array(

	/*
	|--------------------------------------------------------------------------
	| Name
	|--------------------------------------------------------------------------
	|
	| This is your extension name and it is only used for presentational
	| purposes only.
	|
	*/

	'name' => 'Menus',

	/*
	|--------------------------------------------------------------------------
	| Slug
	|--------------------------------------------------------------------------
	|
	| This is your extension unique identifier and should not be changed as
	| it will be recognized as a new extension. Ideally, this should match
	| the folder structure within the extensions folder, but this is
	| completely optional.
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
	| One or two sentences describing the extension for users to view when
	| they are installing the extension.
	|
	*/

	'description' => 'Manage all the menus throughout your website.',

	/*
	|--------------------------------------------------------------------------
	| Version
	|--------------------------------------------------------------------------
	|
	| Version should be a string that can be used with version_compare().
	| This is how the extensions versions are compared.
	|
	*/

	'version' => '2.0.0',

	/*
	|--------------------------------------------------------------------------
	| Requirements
	|--------------------------------------------------------------------------
	|
	| You should list here all the extensions this extension requires to work
	| properly. This is used in conjunction with composer, so you should put
	| the same extension dependencies on your composer.json require key so
	| that they get resolved using composer, however you can use without
	| composer, at which point you'll have to ensure that the required
	| extensions are available.
	|
	*/

	'require' => array(

		'platform/admin',

	),

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
	|	object Composer\Autoload\ClassLoader      $loader
	|	object Illuminate\Foundation\Application  $app
	|
	|
	| Supported: "composer", "platform", "Closure"
	|
	*/

	'autoload' => 'composer',

	/*
	|--------------------------------------------------------------------------
	| URI
	|--------------------------------------------------------------------------
	|
	| You can specify the URI that this extension will respond to.
	|
	| You can choose to specify a single string, where the URI will be matched
	| on the 'admin' and 'public' sections of Platform.
	|
	| You can provide an array with the 'admin' and 'public' keys to specify
	| a different URI for admin and public sections, you can have as many
	| keys as you need in case your applications needs them.
	|
	| You can provide an 'override' which is an array of extensions this
	| extension overrides it's URI from.
	|
	*/

	'uri' => 'menus',

	/*
	|--------------------------------------------------------------------------
	| Register Callback
	|--------------------------------------------------------------------------
	|
	| Closure that is called when the extension is registered. This can do
	| all the needed custom logic upon registering.
	|
	| The closure parameters are:
	|
	|	object Cartalyst\Extensions\ExtensionInterface
	|	object Illuminate\Foundation\Application
	|
	*/

	'register' => function(ExtensionInterface $extension, Application $app)
	{
		// After platform finishes the installation process, we'll
		// loop through each extension that exists and apply our
		// after install and after enable filters on them.
		Installer::after(function()
		{
			foreach (Extensions::allEnabled() as $extension)
			{
				app('Platform\Menus\Observer')->afterInstall($extension);
				app('Platform\Menus\Observer')->afterEnable($extension);
			}
		});

		$app['Platform\Menus\StaticType'] = $app->share(function($app)
		{
			return new Platform\Menus\StaticType($app['url'], $app['view'], $app['translator']);
		});
	},

	/*
	|--------------------------------------------------------------------------
	| Boot Callback
	|--------------------------------------------------------------------------
	|
	| Closure that is called when the extension is booted. This can do
	| all the needed custom logic upon booting.
	|
	| The closure parameters are:
	|
	|	object Cartalyst\Extensions\ExtensionInterface
	|	object Illuminate\Foundation\Application
	|
	*/

	'boot' => function(ExtensionInterface $extension, Application $app)
	{
		Extension::installed(function($extension) use ($app)
		{
			app('Platform\Menus\Observer')->afterInstall($extension);
		});

		Extension::uninstalled(function($extension) use ($app)
		{
			app('Platform\Menus\Observer')->afterUninstall($extension);
		});

		Extension::enabled(function($extension) use ($app)
		{
			app('Platform\Menus\Observer')->afterEnable($extension);
		});

		Extension::disabled(function($extension) use ($app)
		{
			app('Platform\Menus\Observer')->afterDisable($extension);
		});

		app('Platform\Menus\Models\Menu')->registerType($app['Platform\Menus\StaticType']);

		app('Platform\Menus\Models\Menu')->observe(app('Platform\Menus\Observer'));
	},

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
	|	object Cartalyst\Extensions\ExtensionInterface
	|	object Illuminate\Foundation\Application
	|
	*/

	'routes' => function(ExtensionInterface $extension, Application $app)
	{
		Route::group(array('prefix' => '{api}/v1/menus/{slug}'), function()
		{
			Route::get('children', 'Platform\Menus\Controllers\Api\V1\ChildrenController@show');
			Route::put('children', 'Platform\Menus\Controllers\Api\V1\ChildrenController@update');
			Route::get('path', 'Platform\Menus\Controllers\Api\V1\PathController@show');
		});
	},

	/*
	|--------------------------------------------------------------------------
	| Permissions
	|--------------------------------------------------------------------------
	|
	| List of permissions this extension has. These are shown in the user
	| management area to build a graphical interface where permissions
	| may be selected.
	|
	| The admin controllers state that permissions should follow the following
	| structure:
	|
	|     vendor/extension::area.controller@method
	|
	| For example:
	|
	|    platform/users::admin.usersController@index
	|    Platform\Users\Controllers\Admin\UsersController@getIndex
	|
	| These are automatically generated for controller routes however you are
	| free to add your own permissions and check against them at any time.
	|
	| When writing permissions, if you put a 'key' => 'value' pair, the 'value'
	| will be the label for the permission which is displayed when editing
	| permissions.
	|
	*/

	'permissions' => function()
	{
		return array(

			'platform/menus::admin.menusController@index'  => Lang::get('platform/menus::permissions.index'),
			'platform/menus::admin.menusController@grid'   => Lang::get('platform/menus::permissions.grid'),
			'platform/menus::admin.menusController@create' => Lang::get('platform/menus::permissions.create'),
			'platform/menus::admin.menusController@edit'   => Lang::get('platform/menus::permissions.edit'),
			'platform/menus::admin.menusController@delete' => Lang::get('platform/menus::permissions.delete'),

		);
	},

	/*
	|--------------------------------------------------------------------------
	| Widgets
	|--------------------------------------------------------------------------
	|
	| List of custom widgets associated with the extension. Like routes, the
	| value for the widget key may either be a closure or a class & method
	| name (joined with an @ symbol). Of course, Platform will guess the
	| widget class for you, this is just for custom widgets or if you
	| do not wish to make a new class for a very small widget.
	|
	*/

	'widgets' => array(),

	/*
	|--------------------------------------------------------------------------
	| Plugins
	|--------------------------------------------------------------------------
	|
	| List of custom plugins associated with the extension. Like routes, the
	| value for the plugin key may either be a closure or a class & method
	| name (joined with an @ symbol). Of course, Platform will guess the
	| plugin class for you, this is just for custom plugins or if you
	| do not wish to make a new class for a very small plugin.
	|
	*/

	'plugins' => array(),

	/*
	|--------------------------------------------------------------------------
	| Settings
	|--------------------------------------------------------------------------
	|
	| Register any settings for your extension. You can also configure
	| the namespace and group that a setting belongs to.
	|
	*/

	'settings' => function()
	{

	},

	/*
	|--------------------------------------------------------------------------
	| Menus
	|--------------------------------------------------------------------------
	|
	| You may specify the default various menu hierarchy for your extension.
	| You can provide a recursive array of menu children and their children.
	| These will be created upon installation, synchronized upon upgrading
	| and removed upon uninstallation.
	|
	| Menu children are automatically put at the end of the menu for extensions
	| installed through the Operations extension.
	|
	| The default order (for extensions installed initially) can be
	| found by editing app/config/platform.php.
	|
	*/

	'menus' => array(

		'admin' => array(

			array(
				'slug'  => 'admin-menus',
				'name'  => 'Menus',
				'class' => 'fa fa-th-list',
				'uri'   => 'menus',
				'regex' => '/admin\/menus/i',
			),

		),

		'system' => array(

			array(
				'slug'  => 'system-preview',
				'name'  => 'Preview',
				'class' => 'fa fa-home',
				'uri'   => '/',
			),

			array(
				'slug'  => 'system-settings',
				'name'  => 'Settings',
				'class' => 'fa fa-cog',
				'uri'   => 'admin/settings',
				'regex' => '/admin\/settings/i',
			),


			array(
				'slug'  => 'system-logout',
				'name'  => 'Sign Out',
				'class' => 'fa fa-sign-out',
				'uri'   => 'logout',
			),

		),

		'main' => array(

			array(
				'slug'       => 'main-home',
				'name'       => 'Home',
				'class'      => 'fa fa-home',
				'uri'        => '/',
				'visibility' => 'always',
			),

			array(
				'slug'       => 'main-login',
				'name'       => 'Sign In',
				'class'      => 'fa fa-sign-in',
				'uri'        => 'login',
				'visibility' => 'logged_out',
			),

			array(
				'slug'       => 'main-logout',
				'name'       => 'Logout',
				'class'      => 'fa fa-home',
				'uri'        => 'logout',
				'visibility' => 'logged_in',
			),

			array(
				'slug'       => 'main-register',
				'name'       => 'Register',
				'class'      => 'fa fa-pencil',
				'uri'        => 'register',
				'visibility' => 'logged_out',
			),

			array(
				'slug'       => 'main-dashboard',
				'name'       => 'Admin',
				'class'      => 'fa fa-dashboard',
				'uri'        => 'admin',
				'visibility' => 'admin',
			),

		),

	),

);
