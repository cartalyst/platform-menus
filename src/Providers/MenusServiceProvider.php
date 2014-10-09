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

use Extension;
use Extensions;
use Illuminate\Support\ServiceProvider;
use Installer;
use Platform\Menus\Repositories\IlluminateMenuRepository;
use Platform\Menus\Types\StaticType;

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

		$this->app['Platform\Menus\Models\Menu']->registerType($this->app['Platform\Menus\Types\StaticType']);

		$this->app['Platform\Menus\Models\Menu']->observe($observer);

		// Subscribe the registered event handlers
		$this->app['events']->subscribe('Platform\Menus\Handlers\MenuEventHandlerInterface');
	}

	/**
	 * {@inheritDoc}
	 */
	public function register()
	{
		$this->registerMenusValidator();

		$this->registerAfterInstallEvents();

		$this->registerMenuType();

		$this->registerMenuRepository();

		// Register the attributes namespace
		$this->app['platform.attributes.manager']->registerNamespace(
			$this->app['Platform\Menus\Models\Menu']
		);

		$this->registerEventHandlers();
	}

	/**
	 * Register the after install event.
	 *
	 * @return void
	 */
	protected function registerAfterInstallEvents()
	{
		$app = $this->app;

		// After platform finishes the installation process, we'll
		// loop through each extension that exists and apply our
		// after install and after enable filters on them.
		Installer::after(function() use ($app)
		{
			$observer = $app['Platform\Menus\Observer'];

			foreach (Extensions::allEnabled() as $extension)
			{
				$observer->afterInstall($extension);

				$observer->afterEnable($extension);
			}
		}, 10);
	}

	/**
	 * Register the menu type.
	 *
	 * @return void
	 */
	protected function registerMenuType()
	{
		$menuType = 'Platform\Menus\Types\StaticType';

		if ( ! $this->app->bound($menuType))
		{
			$this->app->bind($menuType, function($app)
			{
				return new StaticType($app['url'], $app['view'], $app['translator']);
			});
		}
	}

	/**
	 * Register the menu repository.
	 *
	 * @return void
	 */
	protected function registerMenuRepository()
	{
		$menuRepository = 'Platform\Menus\Repositories\MenuRepositoryInterface';

		if ( ! $this->app->bound($menuRepository))
		{
			$this->app->bind($menuRepository, function($app)
			{
				$model = get_class($app['Platform\Menus\Models\Menu']);

				return (new IlluminateMenuRepository($model, $app['events'], $app['cache']))
					->setValidator($app['Platform\Menus\Validator\MenusValidatorInterface']);
			});
		}
	}

	/**
	 * Register the menus validator.
	 *
	 * @return void
	 */
	protected function registerMenusValidator()
	{
		$binding = 'Platform\Menus\Validator\MenusValidatorInterface';

		if ( ! $this->app->bound($binding))
		{
			$this->app->bind($binding, 'Platform\Menus\Validator\MenusValidator');
		}
	}

	/**
	 * Register the event handlers.
	 *
	 * @return void
	 */
	protected function registerEventHandlers()
	{
		$this->app->bindIf(
			'Platform\Menus\Handlers\MenuEventHandlerInterface',
			'Platform\Menus\Handlers\MenuEventHandler'
		);
	}

}
