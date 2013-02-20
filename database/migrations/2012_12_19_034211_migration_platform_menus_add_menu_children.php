<?php

use Illuminate\Database\Migrations\Migration;
use Platform\Menus\Menu;

class MigrationPlatformMenusAddMenuChildren extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Does the Admin menu exists?
		if (is_null($admin = Menu::find('admin')))
		{
			// Create the Admin menu
			$admin = new Menu(array(
				'slug'      => 'admin',
				'extension' => 'platform/menus',
				'name'      => 'Admin',
			));
			$admin->makeRoot();
		}

		// Create the Admin > System menu
		$menu = new Menu(array(
			'slug'      => 'admin-system',
			'extension' => 'platform/menus',
			'name'      => 'System',
			'driver'    => 'static',
			'uri'       => 'settings',
		));
		$menu->makeLastChildOf($admin);

		// Create the Admin > Menus menu
		$menu = new Menu(array(
			'slug'      => 'admin-menus',
			'extension' => 'platform/menus',
			'name'      => 'Menus',
			'driver'    => 'static',
			'uri'       => 'menus',
		));
		$menu->makeLastChildOf($admin);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Notice we don't delete the 'admin' menu ever.
		$slugs = array('admin-system', 'admin-menus');

		foreach ($slugs as $slug)
		{
			if ($menu = Menu::find($slug))
			{
				$menu->delete();
			}
		}
	}

}
