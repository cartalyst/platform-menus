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

use Illuminate\Database\Migrations\Migration;

class MigrationPlatformMenusInstallMenus extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('menus', function($table)
		{
			// Menu children are type based. You can add multiple types for how
			// each child is rendered. We're going to firstly add all core
			// fields to the menus that are common across all types.
			// When a menu item has a custom type, an event is fired
			// so the type can do whatever it needs to do.
			$table->increments('id');

			// Slug of a menu child. A slug MUST be unique
			// on the menus table. This will be the primary key
			$table->string('slug');

			// Modified Preorder Tree Traversal algorithm.
			// See http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/
			$table->integer('lft');
			$table->integer('rgt');
			$table->integer('menu');

			// Extension that is responsible for a menu child. We use this to
			// disable all menu children with extensions.
			$table->string('extension')->nullable();

			// Name of the child
			$table->string('name')->nullable();

			// Type of the child
			$table->string('type')->default('static');

			$table->boolean('secure')->default(0);

			// Specific to "static" menu children
			$table->string('uri')->nullable();

			// Class for each menu item
			$table->string('class')->nullable();

			// Target for clicked item
			$table->string('target')->default('self');

			// User visibility flag
			$table->string('visibility')->nullable();
			$table->text('groups')->nullable();

			// Enabled
			$table->boolean('enabled')->default(0);


			// Specific to "cms" menu children
			// @todo this logic needs to move
			// and we need to handle this through
			// a bunch of events, so that menus need
			// not know about the cms extensions
			$table->boolean('page_id')->nullable();




			// Timestamps of course
			$table->timestamps();

			// Alright, all fields created. We'll add a
			// primary key, based on the slug field
			$table->unique('slug');

			// Let's add an index on the MPTT fields. This will speed up the
			// reading process even further. We'll need to ensure that
			// MySQL uses the InnoDB engine to support the indexes,
			// other engines aren't affected.
			$table->engine = 'InnoDB';
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
		// Because we are dropping the menus table, we don't need
		// to worry about removing the admin menu first as the
		// entire table will be gone.
		Schema::drop('menus');
	}

}
