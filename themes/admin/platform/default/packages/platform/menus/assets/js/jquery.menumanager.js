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
		// To avoid scope issues, use 'base' instead of 'this'
		// to reference this class from internal events and functions.
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

			// Extend the default options with the
			// provided options.
			base.options = $.extend({},$.MenuManager.defaultOptions, options);

			// Activate Nestable
			$(base.options.nestableSelector).nestable({
				maxDepth : 100,
				expandBtnHTML : false,
				collapseBtnHTML : false
			});

			// Generate the initial children slug
			$(base.options.itemSlugSelector).val(base.generateNewItemSlug());

			// When the root menu name value changes
			$(base.options.rootNameSelector).keyup(function() {

				// Clean the root menu slug value
				base.generateRootSlug($(this).val());

				// Update the new menu item slug value
				base.updateNewItemSlug();

			});

			// When the root menu slug value changes
			$(base.options.rootSlugSelector).on('change', function() {

				// Clean the root menu slug value
				base.generateRootSlug($(this).val());

				// Update the new menu item slug value
				base.updateNewItemSlug();

			});

			// Removes an item
			$(base.options.nestable.itemRemove).live('click', base.removeItem);

			// Adds a new item
			$(base.options.itemAddSelector).on('click', base.addNewItem);

			// Generates the new item slug
			$(base.options.itemNameSelector).keyup(function() {

				$(base.options.itemSlugSelector).val(base.generateNewItemSlug($(this).val()));

			});

			// Show the children details
			$(base.options.children.toggleSelector).live('click', function() {

				$(this).closest(base.options.nestable.itemSelector).find(base.options.nestable.itemDetailsSelector).toggleClass('show');

			});

			// When we submit the form
			base.$el.submit(function(e){
				//e.preventDefault();

				// Append input to the form. It's values are JSON encoded..
				base.$el.append('<input type="hidden" name="' + base.options.hierarchyInputName + '" value=\'' + window.JSON.stringify($(base.options.nestableSelector).nestable('serialize')) + '\'>');

				return true;
			});

		};

		/**
		 * Adds a new item.
		 *
		 * @return void
		 */
		base.addNewItem = function(e) {

			// Prevent the form from being submited
			e.preventDefault();

			// Get the new item data
			name = $(base.options.itemNameSelector).val();
			slug = base.slugify($(base.options.itemSlugSelector).val());

			// Make sure that both child name and slug
			// are not empty.
			if (name != '' && slug != '')
			{
				if (($.inArray(slug, base.options.persistedSlugs) > -1))
				{
					alert('item with this slug already exists');
					// show the error...
				}
				else
				{
					// remove the error...


					// ###################################
					// Add the children...
					// ### find another clean way to do this
					html = '<li class="child dd-item dd3-item" data-slug="' + slug + '">';

						html += '<div class="dd-handle dd3-handle">Drag</div>';

						html += '<div class="dd3-content">' + name + '</div>';

						html += '<div class="child">';
							html += '<div class="dd-handlex teste-handle toggle-children">Toogle Details</div>';
							html += '<div class="child-details">';
								html += '<input type="text" name="children[' + slug + '][name]" value="' + name + '"><br/>';
								html += '<input type="text" name="children[' + slug + '][slug]" value="' + slug + '">';
								html += '<br ><br>';
								html += '<button name="remove" class="remove">Delete</button>';
							html += '</div>';
						html += '</div>';
					html += '</li>';
					// ###################################



					$(base.options.nestableSelector + ' > ol').append(html);

					// Add the item to the array
					base.options.persistedSlugs.push(slug);

					// Clean the new item inputs
					$(base.options.itemNameSelector).val('');
					$(base.options.itemSlugSelector).val(base.generateNewItemSlug());

				}

			}

		};

		/**
		 * Updates the new item slug.
		 *
		 * @return void
		 */
		base.updateNewItemSlug = function() {

			// Get the new item name value
			itemNameValue = $(base.options.itemNameSelector).val();

			// Does this new menu item have a name?
			if (itemNameValue.length == 0)
			{
				// Update the new item slug
				$(base.options.itemSlugSelector).val(base.generateNewItemSlug(itemNameValue));
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
			$(base.options.rootSlugSelector).val(base.generateSlug(value));

		};

		/**
		 * Removes an item.
		 *
		 * @return void
		 */
		base.removeItem = function() {

			// Get this item slug
			//itemSlug = $(this).closest(base.options.nestable.itemSelector).data('slug');
			itemSlug = $(this).closest('.dd-item').data('slug');


			// Remove the item from the array
			base.options.persistedSlugs.splice($.inArray(itemSlug, base.options.persistedSlugs), 1);

			// Remove the item from the menu
			$('.dd-item[data-slug="' + itemSlug + '"]').remove();

		};

		/**
		 * Returns the current `Root item` slug.
		 *
		 * @return string
		 */
		base.getRootSlug = function(string) {

			return $(base.options.rootSlugSelector).val() + base.options.slugSeparator;

		};

		/**
		 * Generates a slug.
		 *
		 * @param  string
		 * @return string
		 */
		base.generateSlug = function(string) {

			// Make sure we have a string
			string = typeof string !== 'undefined' ? string : '';

			// Return the slugified string
			return base.slugify(string);

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
			separator = base.options.slugSeparator;

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

		// Holds all of the existing menu slugs
		persistedSlugs : [],

		// Slug separator
		slugSeparator : '-',

		// Selector that activates the Nestable plugin
		nestableSelector : '#nestable',

		// Root selectors
		rootNameSelector : '#menu-name',
		rootSlugSelector : '#menu-slug',

		// New item
		itemNameSelector : '#newitem-name',
		itemSlugSelector : '#newitem-slug',
		itemAddSelector  : '#newitem-add',

		// Children
		children : {
			toggleSelector : '.toggle-children'
		},

		// Nestable settings
		nestable : {
			// namespace : == nestableSelector
			itemSelector : '.child',
			itemRemove   : '.remove',

			itemDetailsSelector : '.child-details'
		},


		hierarchyInputName: 'children_hierarchy'

	};



	$.fn.MenuManager = function(options) {

		return this.each(function(){
			(new $.MenuManager(this, options));
		});

	};

})(jQuery);
