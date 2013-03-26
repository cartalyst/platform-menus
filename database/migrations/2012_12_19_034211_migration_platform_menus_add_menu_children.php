<?php

use Illuminate\Database\Migrations\Migration;
use Platform\Ui\Menu;

class MigrationPlatformMenusAddMenuChildren extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
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
		$menu = Menu::find('admin-menus');
		$menu->delete();
	}

}
