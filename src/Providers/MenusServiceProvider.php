<?php namespace Platform\Menus\Providers;
/**
 * Part of the Platform Menus extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Platform Menus extension
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Extensions\Extension;
use Cartalyst\Support\ServiceProvider;

class MenusServiceProvider extends ServiceProvider {

	/**
	 * {@inheritDoc}
	 */
	public function boot()
	{
		$this->package('platform/menus', 'platform/menus', __DIR__.'/../..');

		$observer = $this->app['Platform\Menus\Observer'];

		Extension::installed(function($extension) use ($observer)
		{
			$observer->afterInstall($extension);
		});

		Extension::uninstalled(function($extension) use ($observer)
		{
			$observer->afterUninstall($extension);
		});

		Extension::enabled(function($extension) use ($observer)
		{
			$observer->afterEnable($extension);
		});

		Extension::disabled(function($extension) use ($observer)
		{
			$observer->afterDisable($extension);
		});

		$this->app['platform.menus.manager']->registerType(
			$this->app['platform.menus.types.static']
		);

		$this->app['Platform\Menus\Models\Menu']->observe($observer);

		// Subscribe the registered event handlers
		$this->app['events']->subscribe('platform.menus.handler.events');
	}

	/**
	 * {@inheritDoc}
	 */
	public function register()
	{
		$this->registerAfterInstallEvents();

		// Register the attributes namespace
		$this->app['platform.attributes.manager']->registerNamespace(
			$this->app['Platform\Menus\Models\Menu']
		);

		// Register the repository
		$this->bindIf('platform.menus', 'Platform\Menus\Repositories\MenuRepository');

		// Register the menus 'static' type
		$this->bindIf('platform.menus.types.static', 'Platform\Menus\Types\StaticType');

		// Register the data handler
		$this->bindIf('platform.menus.handler.data', 'Platform\Menus\Handlers\DataHandler');

		// Register the validator
		$this->bindIf('platform.menus.validator', 'Platform\Menus\Validator\MenusValidator');

		// Register the event handler
		$this->bindIf('platform.menus.handler.events', 'Platform\Menus\Handlers\EventHandler');

		// Register the manager
		$this->bindIf('platform.menus.manager', 'Platform\Menus\Repositories\ManagerRepository');
	}

	/**
	 * Register the after install event.
	 *
	 * @return void
	 */
	protected function registerAfterInstallEvents()
	{
		// After platform finishes the installation process, we'll
		// loop through each extension that exists and apply our
		// after install and after enable filters on them.
		$this->app['platform.installer']->after(function()
		{
			$observer = $this->app['Platform\Menus\Observer'];

			foreach ($this->app['extensions']->allEnabled() as $extension)
			{
				$observer->afterInstall($extension);

				$observer->afterEnable($extension);
			}
		}, 10);
	}

}
