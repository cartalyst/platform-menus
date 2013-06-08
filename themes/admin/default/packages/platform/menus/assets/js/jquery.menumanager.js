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
		 * @return void
		 */
		base.Initializer = function() {

			/////// WIP
			$('input[type="text"]').keyup(function()
			{

				childrenId = $(this).data('children');

				if (typeof childrenId !== 'undefined')
				{
					$("[data-id='" + childrenId + "']")
						.find(".item-name")
						.html($('#' + childrenId + '_name').val());
				}

			});
			///////




			// Extend the default options with the provided options
			base.options = $.extend({}, $.MenuManager.defaultOptions, options);

			// Initialize Tempo js
			base.options.Tempo = Tempo.prepare('nestable', base.options.tempoSettings);

			// Activate Nestable
			$(base.options.nestable.selector).nestable(base.options.nestable);

			// Generate the initial children slug
			$(base.options.form.children.slug).val(base.generateChildrenSlug());

			// When the root menu name value changes
			$(base.options.form.root.name).keyup(function() {

				// Clean the root menu slug value
				base.generateRootSlug($(this).val());

				// Update the new menu item slug value
				base.updateChildrenSlug();

				base.updateChildrenUri();

			});

			// When the root menu slug value changes
			$(base.options.form.root.slug).on('change', function() {

				// Clean the root menu slug value
				base.generateRootSlug($(this).val());

				// Update the new menu item slug value
				base.updateChildrenSlug();

				base.updateChildrenUri();

			});

			// Adds a new menu item
			$(base.options.form.children.submit).on('click', base.addItem);

			// Removes a menu item
			$(base.options.form.itemRemove).on('click', base.removeItem);

			// Clean the root item name
			$(base.options.form.root.name).on('change', function() {

				$(base.options.form.root.name).val($.trim($(this).val()));

			});

			// Clean the root item slug
			$(base.options.form.root.slug).on('change', function() {

				$(base.options.form.root.slug).val($.trim($(this).val()));

			});

			// Update the new menu item slug
			$(base.options.form.children.name).keyup(function() {

				base.updateChildrenSlug();

				base.updateChildrenUri();

			});

			// Clean the new item name
			$(base.options.form.children.name).on('change', function() {

				$(this).val($.trim($(this).val()));

			});

			// Clean the new item slug
			$(base.options.form.children.slug).on('change', function() {

				$(this).val(base.slugify($(this).val()));

				base.validateChildSlug();

			});

			// When the main form is submited
			base.$el.submit(function(e){

				// Append input to the form. It's values are JSON encoded..
				return base.$el.append('<input type="hidden" name="' + base.options.hierarchyInputName + '" value=\'' + window.JSON.stringify($(base.options.nestable.selector).nestable('serialize')) + '\'>');

			});

		};

		/**
		 * Adds a new menu item.
		 *
		 * @return void
		 * @todo   Add TempoJs, so when we add a new item we
		 *         use the template, instead of this messy code!
		 */
		base.addItem = function(e) {

			// Prevent the form from being submited
			e.preventDefault();

			// Run the before callback
			base.options.beforeAdd();

			// Validate the new children name and the new children slug
			if (base.validateChildName() & base.validateChildSlug())
			{

				// Get the new item data
				name = $.trim($(base.options.form.children.name).val());
				slug = base.slugify($(base.options.form.children.slug).val());
				uri = base.slugify($(base.options.form.children.name).val());
				css_class = $(base.options.form.children.css_class).val();

				// Hide the no children div
				$(base.options.noChildrenSelector).addClass('hide');

				//
				var data = [
					{
						'name'       : name,
						'slug'       : slug,
						'type'       : $('#new-child-type').val(),
						'uri'        : uri,
						'visibility' : $('#new-child-visibility').val(),
						'secure'     : $('#new-child-secure').val(),
						'target'     : $('#new-child-target').val(),
						'css_class'  : css_class,
						'enabled'    : $('#new-child-enabled').val()
					}
				];

				//
				base.options.Tempo.append(data);

				// Add the item to the array
				base.options.persistedSlugs.push(slug);

				// Clean the new item inputs
				$(base.options.form.children.name).val('');
				$(base.options.form.children.slug).val(base.generateChildrenSlug());
				$(base.options.form.children.uri).val('');
				$(base.options.form.children.css_class).val('');

				// Run the after callback
				base.options.afterAdd();

				return true;
			}

			return false;

		};

		/**
		 * Removes a menu item.
		 *
		 * @return void
		 */
		base.removeItem = function() {

			//Close Bootstrap Modal
			$('.modal-backdrop').remove();

			// Run the before callback
			base.options.beforeRemove();

			// Get the item selector
			itemSelector = '.' + base.options.nestable.itemClass;

			// Get this item id
			itemId = $(this).closest(itemSelector).data('id');

			// Get this item slug
			itemSlug = $(this).closest(itemSelector).data('slug');

			// Remove the item from the array
			base.options.persistedSlugs.splice($.inArray(itemSlug, base.options.persistedSlugs), 1);

			// Get both data and item identifier
			dataIdentifier = (typeof itemSlug == 'undefined' ? 'id' : 'slug');
			itemIdentifier = (typeof itemSlug == 'undefined' ? itemId : itemSlug);

			// Find closest item
			var $item = $(itemSelector + '[data-' + dataIdentifier + '="' + itemIdentifier + '"]');
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

			if ($(base.options.nestable.selector + ' > ol > li').length == 0)
			{
				$(base.options.noChildrenSelector).removeClass('hide');
			}

			// Run the after callback
			base.options.afterRemove();

		};

		/**
		 * Updates a menu item.
		 *
		 * @return void
		 */
		base.updateItem = function() {

			// Run the before callback
			base.options.beforeUpdate();

			// ...

			// Run the after callback
			base.options.afterUpdate();

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
		 * Generates the root menu slug, after
		 * the root menu name has been updated.
		 *
		 * @param  string
		 * @return void
		 */
		base.generateRootSlug = function(string) {

			// Update the current menu slug
			$(base.options.form.root.slug).val(base.generateSlug(string));

		};

		/**
		 * Generates a slug.
		 *
		 * @param  string
		 * @return string
		 */
		base.generateSlug = function(string) {

			// Trim the string
			string = $.trim(string);

			// Return the slugified string
			return base.slugify(typeof string !== 'undefined' ? string : '');

		};

		/**
		 * Generates a new item slug based
		 * on the root item slug.
		 *
		 * @param  string
		 * @return string
		 */
		base.generateChildrenSlug = function(string) {

			// Make sure we have a string
			string = typeof string !== 'undefined' ? string : '';

			// Generate the slug and return it
			return base.getRootSlug() + base.generateSlug(string);

		};

		/**
		 * Updates the new item slug.
		 *
		 * @param  string
		 * @return void
		 */
		base.updateChildrenSlug = function(string) {

			//
			if (typeof string == 'undefined')
			{
				// Get the new item name value
				string = $(base.options.form.children.name).val();
			}

			// Generate the slug
			slug = base.generateChildrenSlug(string);

			// Update the new item slug
			$(base.options.form.children.slug).val(slug);

			// Check if the slug alread exists
			if (($.inArray(slug, base.options.persistedSlugs) > -1))
			{
				// Shdow the errors
				//base.showError(base.options.form.children.name);
				base.showError(base.options.form.children.slug);
			}
			else
			{
				// Remove the errors
				//base.hideError(base.options.form.children.name);
				base.hideError(base.options.form.children.slug);
			}

		};

		base.updateChildrenUri = function(string) {

			//
			if (typeof string == 'undefined')
			{
				// Get the new item name value
				string = $(base.options.form.children.name).val();
			}

			// Generate the slug
			slug = base.generateSlug(string);

			// Update the new item uri
			$(base.options.form.children.uri).val(slug);

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

		/**
		 * Validates the children name.
		 *
		 * @return bool
		 */
		base.validateChildName = function(){

			name = $.trim($(base.options.form.children.name).val());

			if (name.length < 3)
			{
				// Show the error
				base.showError(base.options.form.children.name);

				return false;
			}

			// Hide the error
			base.hideError(base.options.form.children.name);

			return true;

		};

		/**
		 * Validates the children slug.
		 *
		 * @return bool
		 */
		base.validateChildSlug = function(){

			slug = base.slugify($(base.options.form.children.slug).val());

			if (slug.length < 3)
			{
				// Show the error
				base.showError(base.options.form.children.slug);

				return false;
			}

			// Check if this slug already exists
			if (($.inArray(slug, base.options.persistedSlugs) > -1))
			{
				// Show the error
				base.showError(base.options.form.children.slug);

				return false;
			}

			// Hide the error
			base.hideError(base.options.form.children.slug);

			return true;

		};

		/**
		 * Show the error on an input.
		 *
		 * @param  string
		 * @return void
		 */
		base.showError = function(input) {

			$(input).addClass('error').closest(base.options.controlGroupSelector).addClass('error');

		};

		/**
		 * Hides the error on an input.
		 *
		 * @param  string
		 * @return void
		 */
		base.hideError = function(input) {

			$(input).removeClass('error').closest(base.options.controlGroupSelector).removeClass('error');

		};

		// Run initializer
		base.Initializer();

	};



	$.MenuManager.defaultOptions = {

		// Selector for control groups that wrap inputs
		controlGroupSelector : '.control-group',
		//// leaving it here, for now...

		noChildrenSelector: '#no-children',

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

			// New Children elements
			children : {
				name      : '#new-child_name',
				slug      : '#new-child_slug',
				uri       : '#new-child_uri',
				css_class : '#new-child_css_class',

				submit : '#new-child-add'
			},

			// Selector for removing menu items
			itemRemove : '.remove'

		},

		// Tempo object
		Tempo : null,

		// Tempo settings
		tempoSettings : {
			'var_braces': '\\[\\[\\]\\]',
			'tag_braces': '\\[\\?\\?\\]'
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

		hierarchyInputName: 'tree',

		/**
		 * Event called before we add a new menu item.
		 *
		 * @return void
		 */
		beforeAdd : function() {},

		/**
		 * Event called after we add a new menu item.
		 *
		 * @return void
		 */
		afterAdd : function() {},

		/**
		 * Event called before we remove a menu item.
		 *
		 * @return void
		 */
		beforeRemove : function() {},

		/**
		 * Event called after we remove a menu item.
		 *
		 * @return void
		 */
		afterRemove : function() {},

		/**
		 * Event called before we update a menu item.
		 *
		 * @return void
		 */
		beforeUpdate : function() {},

		/**
		 * Event called after we update a menu item.
		 *
		 * @return void
		 */
		afterUpdate : function() {}

	};



	$.fn.MenuManager = function(options) {

		return this.each(function(){
			(new $.MenuManager(this, options));
		});

	};

})(jQuery);
