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


/**
 *
 * TODO LIST:
 * 	- Updating : Add validation to the inputs
 *  - Updating : Make sure it generates a new slug
 *  - Adding / Updating : Show users groups when visibility is "logged in" or "admin only"
 *  - Adding / Updating : Show pages list dropdown when the Item type is set to Page
 *  - Updating : Make sure the parent is selected correctly on the dropdown
 *
 */


;(function($, window, document, undefined) {

	'use strict';

	/**
	 * Default settings
	 *
	 * @var array
	 */
	var defaults = {

		// Holds all of the existing menu slugs
		persistedSlugs : [],

		// Slug separator
		slugSeparator : '-',

		// Form elements
		form : {

			// This is the name of the input that is submitted with the
			// children items, this contains the children hierarchy.
			tree : 'menu-tree',

			// Root elements
			root : {

				name : '#menu-name',
				slug : '#menu-slug',

			},

			// New children elements
			children : {

				//error_class : 'error',

				name : {
					input: '#new-child_name',
					rules :	['required']
				},

				slug : {
					input : '#new-child_slug',
					rules :	['required']
				},

				////////////////////////////////////////////////////////
				// this needs to be changed to something more dynamic,
				// since we can have as many types..
				type : {
					input : '#new-child_type'
				},

				uri : {
					input : '#new-child_uri'
				},
				////////////////////////////////////////////////////////

				visibility : {
					input : '#new-child_visibility'
				},

				secure : {
					input : '#new-child_secure'
				},

				target : {
					input : '#new-child_target'
				},

				klass : {
					input : '#new-child_class'
				},

				enabled : {
					input : '#new-child_enabled'
				},

			},


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

		// TempoJS settings
		tempo : {

			mainSelector : 'nestable',
			formsSelector : 'forms',

			var_braces : '\\[\\[\\]\\]',
			tag_braces : '\\[\\?\\?\\]'

		},

		// Modal window elements
		modal : {


		},

		// Are we saving the whole menu?
		isSaving : false,

		// Do we have unsaved changes?
		unsavedChanges : false

	};

	function MenuManager(menu, options) {

		// Extend the default options with the provided options
		this.opt = $.extend({}, defaults, options);

		// Cache the form selector
		this.$form = menu;

		// Initialize the Menu Manager
		this.initializer();

	}

	MenuManager.prototype = {

		/**
		 * Initializes the Menu Manager.
		 *
		 * @return void
		 */
		initializer : function() {

			// Check dependencies
			this.checkDependencies();

			// Prepare TempoJS
			this.TempoJsMain = Tempo.prepare(this.opt.tempo.mainSelector, this.opt.tempo);
			this.TempoJsForms = Tempo.prepare(this.opt.tempo.formsSelector, this.opt.tempo);

			// Activate Nestable
			$(this.opt.nestable.selector).nestable(this.opt.nestable).on('change', this.nestableChangeCallback);

			// Initialize the event listeners
			this.events();

		},

		/**
		 * Checks if the required dependencies are available.
		 *
		 * @return void
		 */
		checkDependencies : function() {

			if ( ! String.prototype.slugify)
			{
				// bahh Slugify is not defined
			}

		},



		nestableChangeCallback : function(e) {

			// once an item moves, we need to update it's parent id and
			// repopulate all the dropdowns again, just to make sure
			// they are all with the correct information.

			console.log(e);

		},


		spacers : function(level) {

			var spacers = '';

			for(var j=0; j < level * 3; j++)
			{
				spacers += '&nbsp;';
			}

			return spacers;

		},

		convertToDropdown : function(UL, level) {

			// Avoid scope issues
			var self = this;

			var dropdown = '';

			UL.children('li').each(function () {

				var id = $(this).data('item-id');

				var text = self.spacers(level) + $(this).find('[data-item="' + id + '"]').text();

				dropdown += '<option value="' + id + '">' + text + '</option>';

				var children = $(this).children('ol');

				if (children.length > 0)
				{
					dropdown += self.convertToDropdown(children, level + 1);
				}
			});

			return dropdown;

		},

		renderParentsDropdowns : function() {

			$('[data-parents]').html('<option value="0">-- Top Level --</option>' + this.convertToDropdown($(this.opt.nestable.selector + ' > ol'), 0));

		},

		/**
		 * Initializes all the event listeners.
		 *
		 * @return void
		 */
		events : function() {

			// Avoid scope issues
			var self = this;

			var $document = $(document);

			// Get the options
			var options = self.opt;

			// Get the form options
			var formOpt = options.form

			// Generate the initial children slug
			self.slugify($(formOpt.root.slug).val(), formOpt.children.slug.input);

			// Set a bind to check if we have unsaved changes when
			// we are about to leave the menu manager page.
			$(window).bind('beforeunload', function() {

				if (options.unsavedChanges & ! options.isSaving)
				{
					return 'You have unsaved changes.';
				}

			});


			// Pre-render the parents dropdown
			self.renderParentsDropdowns();

			// Clean the input values when there are changes
			$document.on('change', 'input[type="text"]', function() {

				$(this).val($.trim($(this).val()));

			});

			// When menu children data get's updated
			$document.on('keyup', 'input[type="text"]', function() {

				// need to fix this so it doesn't detect the new children inputs, not the ones
				// we added, but the one we are adding!!
				/*
				if ($(this).data('children'))
				{
					// Get the children identifier
					var childrenId = $(this).data('children');

					//
					if (typeof childrenId !== 'undefined')
					{
						$('[data-id="' + childrenId + '"], [data-slug="' + childrenId + '"]')
							.find(".item-name:eq(0)")
							.html($('#' + childrenId + '_name').val());
					}
				}
				*/

			});

			// When the value of the root name input changes
			$document.on('keyup', formOpt.root.name, function() {

				// Update the root slug value
				self.slugify($(this).val(), formOpt.root.slug);

				// Update the new menu item inputs
				self.updateNewFormInputs();

			})

			// When the value of the root slug input changes
			$document.on('change', formOpt.root.slug, function() {

				// Clean the root slug value
				self.slugify($(this).val(), formOpt.root.slug);

				// Update the new menu item inputs
				self.updateNewFormInputs();

			});




			/**
			 * Shows a menu item form box.
			 *
			 * @return void
			 */
			$document.on('click', '[data-item]', function() {

				// Hide the root form
				self.hideRootForm();

				// Close all the other item forms  boxes
				$('[data-item-form]').addClass('hide');

				// Get the item id
				var itemId = $(this).data('item');

				// Get the item form box
				var itemBox = $('[data-item-form=' + itemId + ']');

				// Get the parent id
				var parentId = itemBox.data('item-parent');

				// Show the form item box
				itemBox.removeClass('hide');

				// Change the selected item on the dropdown
				itemBox.find('[data-parents]').val(parentId);

			});


			/**
			 * Toggle the options on an item box.
			 *
			 * @return void
			 */
			$document.on('click', '[data-toggle-options]', function(e) {

				// Prevent the form from being submitted
				e.preventDefault();

				// Get the item id
				var itemId = $(this).data('toggle-options');

				// Get the element options
				var element = $('[data-item-form="' + itemId + '"]').find('[data-options]');

				// Should we hide or show the options element?
				if (element.hasClass('hide'))
				{
					element.removeClass('hide');
				}
				else
				{
					element.addClass('hide');
				}

			});


			/**
			 * Hides a menu item form box.
			 *
			 * @return void
			 */
			$document.on('click', '[data-item-close]', function() {

				// Show the root form
				self.showRootForm();

				// Get the item id
				var itemId = $(this).data('item-close');

				// Close the item form box
				$('[data-item-form="' + itemId + '"]').addClass('hide');

			});


			/**
			 * Shows the add new item form box.
			 *
			 * @return void
			 */
			$document.on('click', '[data-item-add]', function(e) {

				// Prevent the form from being submitted
				e.preventDefault();

				// Hide the root form
				self.hideRootForm();

				// Show the add item form
				$('[data-item-form="new-child"]').removeClass('hide');

			});


			/**
			 * Adds a new menu item.
			 *
			 * @return void
			 */
			$document.on('click', '[data-item-create]', function(e) {

				// Prevent the form from being submitted
				e.preventDefault();

				// Generate the children slug
				var slug = $(formOpt.children.slug.input).val().slugify();

				// Check if this an unique slug
				if ( ! self.isUniqueSlug(slug))
				{
					alert('Unique slug, fix it...');
				}

				// Check if the form is valid
				else if (self.validateInputs(formOpt.children) )
				{
					// Get the parent id
					var parentId = $('#new-child_parent').val();

					// Prepare the new item data
					var data = {
						'name'       : $.trim($(formOpt.children.name.input).val()),
						'slug'       : slug,
						'enabled'    : $(formOpt.children.enabled.input).val(),

						'type'       : $(formOpt.children.type.input).val(),
						'uri'        : $.trim($(formOpt.children.uri.input).val()),
						'secure'     : $(formOpt.children.secure.input).val(),

						'visibility' : $(formOpt.children.visibility.input).val(),

						'attribute_id'         : '',
						'attribute_name'       : '',
						'attribute_class'      : $.trim($(formOpt.children.klass.input).val()),
						'attribute_title'      : '',
						'attribute_target'     : $(formOpt.children.target.input).val()
					};

					// Append the new menu item
					self.TempoJsMain.append(data);
					self.TempoJsForms.append(data);

					// Add the item to the array
					options.persistedSlugs.push(slug);

					// Clean the new form item inputs
					$(formOpt.children.name.input).val('');
					self.slugify($(formOpt.root.slug).val(), formOpt.children.slug.input);
					$(formOpt.children.uri.input).val('');
					$(formOpt.children.klass.input).val('');

					// Move the item to the correct destination
					$('[data-item-id="' + slug + '"]').appendTo('[data-item-id="' + parentId + '"] > ol');

					// We have unsaved changes
					options.unsavedChanges = true;

					// Show the root form
					self.showRootForm();

					// Show the add button
					$('[data-item-add]').removeClass('hide');

					// Hide the no items container
					$('[data-no-items]').addClass('hide');

					// Hide the add new item form
					$('[data-item-form="new-child"]').addClass('hide');

					// Refresh the parents dropdowns
					self.renderParentsDropdowns();
				}

			});


			/**
			 * Updates a menu item.
			 *
			 * @return void
			 */
			$document.on('click', '[data-item-update]', function(e) {

				// Prevent the form from being submitted
				e.preventDefault();

				// Get the form id
				var formId = $(this).data('item-update');

				// Get the current slug
				var currentSlug = $('#' + formId + '_current_slug').val();

				// Get the new slug
				var slug = $('#' + formId + '_slug').val().slugify();

				// Check if this an unique slug
				if ( ! self.isSameSlug(currentSlug, slug) & ! self.isUniqueSlug(slug))
				{
					alert('Unique slug, fix it...');
				}

				else
				{
					// Remove the item from the array, because we
					// might have changed the slug.
					options.persistedSlugs.splice($.inArray(slug, options.persistedSlugs), 1);

					// Add the item slug to the array
					options.persistedSlugs.push(slug);

					// Update the current slug input value
					$('#' + formId + '_current_slug').val(slug);

					// Show the root form
					self.showRootForm();

					// We have unsaved changes
					options.unsavedChanges = true;

					// Get the item id
					var id = $(this).closest('[data-item-form]').data('item-form');

					// Hide the form item box
					$('[data-item-form=' + id + ']').addClass('hide');
				}

			});


			/**
			 * Removes a menu item.
			 *
			 * @return void
			 */
			$document.on('click', '[data-item-remove]', function(e) {

				// Prevent the form from being submitted
				e.preventDefault();

				// Confirmation message
				var message = 'Are you sure you want to delete this menu item?';

				// Confirm if the user wants to remove the item
				if (confirm(message) == true)
				{
					// Get this item id
					var itemId = $(this).data('item-remove');

					// Find the item
					var item = $('[data-item="' + itemId + '"]').closest('li');
					var list = item.children(options.nestable.listNodeName);

					// Check if we have children
					if (list.length > 0)
					{
						// Grab the list's children items and put them after this item
						var childItems = list.children(options.nestable.itemNodeName);
						childItems.insertAfter(item);
					}

					// Remove the item from the array
					options.persistedSlugs.splice($.inArray(itemId, options.persistedSlugs), 1);

					// Remove the item from the menu
					item.remove();

					// Check if we have children
					if ($(options.nestable.selector + ' > ol > li').length == 0)
					{
						$('[data-item-add]').addClass('hide');
						$('[data-no-items]').removeClass('hide').find('[data-item-add]').removeClass('hide');
					}

					// Remove the item form
					$('[data-item-form="' + itemId + '"]').remove();

					// Show the root form
					self.showRootForm();

					// We have unsaved changes
					options.unsavedChanges = true;

					// Refresh the parents dropdowns
					self.renderParentsDropdowns();
				}

			});


			/**
			 * Process the whole form.
			 *
			 * @return object
			 */
			$document.on('submit', self.$form, function() {

				// We are submitting the form
				options.isSaving = true;

				// Append input to the form. It's values are JSON encoded..
				return $(self.$form).append('<input type="hidden" name="' + options.form.tree + '" value=\'' + window.JSON.stringify($(options.nestable.selector).nestable('serialize')) + '\'>');

			});

		},

		/**
		 * Compares if the provided slugs are the same.
		 *
		 * @param  string  currentSlug
		 * @param  string  newSlug
		 * @return bool
		 */
		isSameSlug : function(currentSlug, newSlug) {

			return currentSlug === newSlug ? true : false;

		},

		/**
		 * Checks if the provided slug is unique on the system.
		 *
		 * @param  string  slug
		 * @return bool
		 */
		isUniqueSlug : function(slug) {

			var self = this;

			return $.inArray(slug, self.opt.persistedSlugs) > -1 ? false : true;

		},

		/**
		 *
		 *
		 * @return void
		 */
		updateNewFormInputs : function() {

			var self = this;

			var options = self.opt.form.children;

			// Generate a new slug based on the root menu slug
			var newSlug = self.getRootSlug() + ' ' + $(options.name.input).val();

			// Update the new item slug
			self.slugify(newSlug, options.slug.input);

		},

		/**
		 * Returns the root slug.
		 *
		 * @return string
		 */
		getRootSlug : function() {

			var self = this;

			return $(self.opt.form.root.slug).val();

		},

		/**
		 * Slugifies the provided value and stores it on the provided input.
		 *
		 * @param  string  value
		 * @param  string  input
		 * @return void
		 */
		slugify : function(value, input) {

			$(input).val(value.slugify());

		},

		/**
		 * Validates the provided inputs with the provided rules.
		 *
		 * @param  array  inputs
		 * @return bool
		 */
		validateInputs : function(inputs) {

			var self = this;

			var failedInputs = [];

			// Loop through the inputs
			$.each(inputs, function(input, value)
			{
				// Does this input have rules?
				if (typeof value.rules !== 'undefined')
				{
					// Loop through the rules
					$.each(value.rules, function(key, rule)
					{
						if (rule == 'required' && $(value.input).val() == '')
						{
							self.showError(value.input);

							failedInputs.push(value.input)
						}
						else
						{
							self.hideError(value.input);
						}
					});
				}
			});

			return failedInputs.length >= 1 ? false : true;

		},

		/**
		 * Shows the root form box.
		 *
		 * @return void
		 */
		showRootForm : function() {

			$('[data-root-form]').removeClass('hide');

		},

		/**
		 * Hides the root form box.
		 *
		 * @return void
		 */
		hideRootForm : function() {

			$('[data-root-form]').addClass('hide');

		},

		showError : function(input) {

			$(input).parent().addClass('error');

		},

		hideError : function(input) {

			$(input).parent().removeClass('error');

		}

	};

	$.menumanager = function(menu, options) {
		return new MenuManager(menu, options);
	};

})(jQuery, window, document);
