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

(function($){

	$.MenuManager = function(el, options) {
		// To avoid scope issues, we use 'base' instead of 'this' to
		// reference this class from internal events and functions.
		var base = this;

		// Access to jQuery and DOM versions of element
		base.$el = $(el);
		base.el  = el;

		// Add a reverse reference to the DOM object
		base.$el.data('MenuManager', base);

		/**
		 * Initializer
		 *
		 * @retrun void
		 */
		base.Initializer = function() {

			// Extend the default options with the provided options
			base.options = $.extend({}, $.MenuManager.defaultOptions, options);

			// Activate Nestable
			$(base.options.nestable.selector).nestable(base.options.nestable);

			// Generate the initial children slug
			$(base.options.form.newItem.slug).val(base.generateNewItemSlug());

			// When the root menu name value changes
			$(base.options.form.root.name).keyup(function() {

				// Clean the root menu slug value
				base.generateRootSlug($(this).val());

				// Update the new menu item slug value
				base.updateNewItemSlug();

			});

			// When the root menu slug value changes
			$(base.options.form.root.slug).on('change', function() {

				// Clean the root menu slug value
				base.generateRootSlug($(this).val());

				// Update the new menu item slug value
				base.updateNewItemSlug();

			});

			// Adds a new menu item
			$(base.options.form.newItem.submit).on('click', base.addNewItem);

			// Removes a menu item
			$(base.options.form.itemRemove).live('click', base.removeItem);

			// Update the new menu item slug
			$(base.options.form.newItem.name).keyup(function() {

				base.updateNewItemSlug();

			});

			// Clean the root item name
			$(base.options.form.root.name).on('change', function() {

				$(base.options.form.root.name).val($.trim($(this).val()));

			});

			// Clean the root item slug
			$(base.options.form.root.slug).on('change', function() {

				$(base.options.form.root.slug).val($.trim($(this).val()));

			});

			// Clean the new item name
			$(base.options.form.newItem.name).on('change', function() {

				$(base.options.form.newItem.name).val($.trim($(this).val()));

			});

			// Clean the new item slug
			$(base.options.form.newItem.slug).on('change', function() {

				$(base.options.form.newItem.slug).val($.trim($(this).val()));

			});

			// When the main form is submited
			base.$el.submit(function(e){

				// Append input to the form. It's values are JSON encoded..
				return base.$el.append('<input type="hidden" name="' + base.options.hierarchyInputName + '" value=\'' + window.JSON.stringify($(base.options.nestable.selector).nestable('serialize')) + '\'>');

			});

		};

		/**
		 * Adds a new item.
		 *
		 * @return void
		 * @todo   Add TempoJs, so when we add a new item we
		 *         use the template, instead of this messy code!
		 */
		base.addNewItem = function(e) {

			// Prevent the form from being submited
			e.preventDefault();

			// Get the new item data
			name = $.trim($(base.options.form.newItem.name).val());
			slug = base.slugify($(base.options.form.newItem.slug).val());

			// Make sure that both child name and slug
			// are not empty.
			if (name != '' && slug != '')
			{
				// Check if this slug already exists
				if (($.inArray(slug, base.options.persistedSlugs) > -1))
				{
					// Show the errors
					$(base.options.form.newItem.name).addClass('error').closest(base.options.controlGroupSelector).addClass('error');
					$(base.options.form.newItem.slug).addClass('error').closest(base.options.controlGroupSelector).addClass('error');

					return false;
				}

				// Remove the errors
				$(base.options.form.newItem.name).removeClass('error').closest(base.options.controlGroupSelector).removeClass('error');
				$(base.options.form.newItem.slug).removeClass('error').closest(base.options.controlGroupSelector).removeClass('error');



				// ###################################
				// Add the children...
				// ### find another clean way to do this
				html = '<li class="item" data-slug="' + slug + '">';
					html += '<div class="item-dd-handle">Drag</div>';

					html += '<div class="item-toggle">Toogle Details</div>';

					html += '<div class="item-name">' + name + '</div>';

					html += '<div class="item-details">';
						html += '<input type="text" name="children[' + slug + '][name]" value="' + name + '"><br/>';
						html += '<input type="text" name="children[' + slug + '][slug]" value="' + slug + '">';
						html += '<br ><br>';
						html += '<button name="remove" class="remove">Delete</button>';
					html += '</div>';
				html += '</li>';
				$(base.options.nestable.selector + ' > ol').append(html);
				// ###################################



				// Add the item to the array
				base.options.persistedSlugs.push(slug);

				// Clean the new item inputs
				$(base.options.form.newItem.name).val('');
				$(base.options.form.newItem.slug).val(base.generateNewItemSlug());
			}
			else
			{
				$(base.options.form.newItem.name).addClass('error').closest(base.options.controlGroupSelector).addClass('error');
				$(base.options.form.newItem.slug).addClass('error').closest(base.options.controlGroupSelector).addClass('error');

				return false;
			}

		};

		/**
		 * Updates the new item slug.
		 *
		 * @return void
		 */
		base.updateNewItemSlug = function() {

			// Get the new item name value
			itemNameValue = $(base.options.form.newItem.name).val();

			//
			newSlug = base.generateNewItemSlug(itemNameValue);

			// Update the new item slug
			$(base.options.form.newItem.slug).val(newSlug);

			//
			if (($.inArray(newSlug, base.options.persistedSlugs) > -1))
			{
				// Show the errors
				$(base.options.form.newItem.name).addClass('error').closest(base.options.controlGroupSelector).addClass('error');
				$(base.options.form.newItem.slug).addClass('error').closest(base.options.controlGroupSelector).addClass('error');
			}
			else
			{
				// Remove the errors
				$(base.options.form.newItem.name).removeClass('error').closest(base.options.controlGroupSelector).removeClass('error');
				$(base.options.form.newItem.slug).removeClass('error').closest(base.options.controlGroupSelector).removeClass('error');
			}

		};

		/**
		 * Generates the root menu slug, after
		 * the root menu name has been updated.
		 *
		 * @param  string
		 * @return void
		 */
		base.generateRootSlug = function(value) {

			// Update the current menu slug
			$(base.options.form.root.slug).val(base.generateSlug(value));

		};

		/**
		 * Removes an item.
		 *
		 * @return void
		 */
		base.removeItem = function() {

			// Get the item selector
			itemSelector = '.' + base.options.nestable.itemClass;

			// Get this item slug
			itemSlug = $(this).closest(itemSelector).data('slug');

			// Remove the item from the array
			base.options.persistedSlugs.splice($.inArray(itemSlug, base.options.persistedSlugs), 1);

			// Find closest item
			var $item = $(itemSelector + '[data-slug="' + itemSlug + '"]');
			var $list = $item.children(base.options.nestable.listNodeName);

			// Check if we have children
			if ($list.length > 0)
			{
				// Grab the list's children items and put them after this item
				$childItems = $list.children(base.options.nestable.itemNodeName);
				$childItems.insertAfter($item);
			}

			// Remove the item from the menu
			$item.remove();

		};

		/**
		 * Returns the current `Root item` slug.
		 *
		 * @return string
		 */
		base.getRootSlug = function() {

			rootSlug = $.trim($(base.options.form.root.slug).val());

			if (rootSlug.length >= 1)
			{
				rootSlug += base.options.slugSeparator;
			}

			return rootSlug;

		};

		/**
		 * Generates a slug.
		 *
		 * @param  string
		 * @param  bool
		 * @return string
		 */
		base.generateSlug = function(string, includeSeparator) {

			// Trim the string
			string = $.trim(string);

			// Make sure we have a string
			string = base.slugify(typeof string !== 'undefined' ? string : '');

			// Do we want to include the slug separator?
			if (typeof includeSeparator !== 'undefined' ? includeSeparator : false)
			{
				string += base.options.slugSeparator;
			}

			// Return the slugified string
			return string;

		};

		/**
		 * Generates a new item slug based
		 * on the root item slug.
		 *
		 * @param  string
		 * @return string
		 */
		base.generateNewItemSlug = function(string) {

			// Make sure we have a string
			string = typeof string !== 'undefined' ? string : '';

			// Generate the slug and return it
			return base.getRootSlug() + base.generateSlug(string);

		};

		/**
		 * Slugify a string.
		 *
		 * @param  string
		 * @return string
		 */
		base.slugify = function(string) {

			// Make sure we have a slug separator
			separator = (typeof base.options.slugSeparator !== 'undefined' ? base.options.slugSeparator : '-');

			// Converts a string to lowercase and
			// removes spaces.
			string = string.toLowerCase().replace(/^\s+|\s+$/g, '');

			// Remove accents
			var from = 'ĺěščřžýťňďàáäâèéëêìíïîòóöôùůúüûñç·/_,:;';
			var to   = 'lescrzytndaaaaeeeeiiiioooouuuuunc------';
			for (var i = 0, l = from.length; i < l; i++)
			{
				string = string.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
			}

			// Return the slugified string
			return string.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
					.replace(/\s+/g, separator) // collapse whitespace and replace by _
					.replace(/-+/g, separator) // collapse dashes
					.replace(new RegExp(separator + '+$'), '') // Trim separator from start
					.replace(new RegExp('^' + separator + '+'), ''); // Trim separator from end

		};

		// Run initializer
		base.Initializer();

	};



	$.MenuManager.defaultOptions = {

		// Selector for control groups that wrap inputs
		controlGroupSelector : '.control-group',
		//// leaving it here, for now...


		// Holds all of the existing menu slugs
		persistedSlugs : [],

		// Slug separator
		slugSeparator : '-',

		// Form elements
		form : {

			// Root elements
			root : {
				name : '#menu-name',
				slug : '#menu-slug'
			},

			// New item elements
			newItem : {
				name   : '#newitem-name',
				slug   : '#newitem-slug',
				submit : '#newitem-add'
			},

			// Selector for removing menu items
			itemRemove : '.remove'

		},

		// Nestable settings
		nestable : {
			selector        : '#nestable',
			listNodeName    : 'ol',
			itemNodeName    : 'li',
			rootClass       : 'nestable',
			listClass       : 'items',
			itemClass       : 'item',
			dragClass       : 'item-dd-drag',
			handleClass     : 'item-dd-handle',
			collapsedClass  : 'item-dd-collapsed',
			placeClass      : 'item-dd-placeholder',
			noDragClass     : 'item-dd-nodrag',
			emptyClass      : 'item-dd-empty',
			expandBtnHTML   : false,
			collapseBtnHTML : false,
			group           : 0,
			maxDepth        : 100
		},

		hierarchyInputName: 'children_hierarchy'

	};



	$.fn.MenuManager = function(options) {

		return this.each(function(){
			(new $.MenuManager(this, options));
		});

	};

})(jQuery);
