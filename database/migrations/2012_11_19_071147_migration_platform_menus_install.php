<?php

use Illuminate\Database\Migrations\Migration;

class MigrationPlatformMenusInstall extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('menus', function($table)
		{
			// Menu children are "driver" based. You can add
			// multiple drivers for how each child is rendered.
			// We're going to firstly add all core fields to the
			// menus that are common across all drivers

			// Slug of a menu child. A slug MUST be unique
			// on the menus table. This will be the primary key
			$table->string('slug');

			// Modify Preorder Tree Traversal algorithm.
			// See http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/
			$table->integer('lft');
			$table->integer('rgt');
			$table->integer('menu');

			// Extension that is responsible for a menu
			// child. We use this to disable all menu children
			// with extnesions
			$table->string('extension')->nullable();

			// Name of the child
			$table->string('name');

			// Driver for each menu child
			$table->string('driver');

			// Visibility for a menu item, 'always',
			// 'logged_in', 'admin' etc
			$table->string('visibility');

			// Target for clicked item, 'self',
			// 'parent', 'blank' etc
			$table->string('target');

			// Class for each menu item
			$table->string('class')->nullable();

			// Timestamps of course
			$table->timestamps();

			// Static menu item driver specific
			$table->string('uri')->nullable();
			
			// Page menu item driver specific
			$table->string('page_id')->nullable();

			// Alright, all fields created. We'll add a
			// primary key, based on the slug field
			$table->primary('slug');

			// Let's add an index on the MPTT fields
			$table->unique(array('lft', 'rgt', 'menu'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('menus');
	}

}