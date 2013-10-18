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

;(function($, window, document, undefined) {

	'use strict';

	/**
	 * Default settings
	 *
	 * @var array
	 */
	var defaults = {

		noChildrenSelector: '#no-children',

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

				submit : '#menu-save'

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

				submit : '#new-child_add'

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

		// TempoJS settings
		tempo : {

			selector : 'nestable',

			var_braces : '\\[\\[\\]\\]',
			tag_braces : '\\[\\?\\?\\]'

		},

		// Modal window elements
		modal : {


		},

		// Do we have unsaved changes?
		unsaved_changes : false

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
			this.TempoJs = Tempo.prepare(this.opt.tempo.selector, this.opt.tempo);

			// Activate Nestable
			$(this.opt.nestable.selector).nestable(this.opt.nestable);

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
			self.slugifyValue($(formOpt.root.slug).val(), formOpt.children.slug.input);

			// Set a bind to check if we have unsaved changes when
			// we are about to leave the page.
			$(window).bind('beforeunload', function()
			{
				if (options.unsaved_changes)
				{
					return 'You have unsaved changes.';
				}
			});


			// When we click on an item, we should show up his
			// form box on the sidebar.
			$document.on('click', '.item-name', function()
			{
				// Hide the root form
				self.hideRootForm();

				// Close all the other item forms  boxes
				$('[data-item-form]').addClass('hide');

				// Get the item id
				var id = $(this).closest('[data-item-id]').data('item-id');

				// Show the form item box
				$('[data-item-form=' + id + ']').removeClass('hide');
			});


			$document.on('click', '[data-item-add]', function()
			{
				// Hide the root form
				self.hideRootForm();

				///////// add the item
			});


			$document.on('click', '[data-item-update]', function()
			{
				// Show the root form
				self.showRootForm();

				// Get the item id
				var id = $(this).closest('[data-item-form]').data('item-form');

				///////// update the item

				// Hide the form item box
				$('[data-item-form=' + id + ']').addClass('hide');
			});


			$document.on('click', '[data-item-remove]', function()
			{
				// Show the root form
				self.showRootForm();

				///////// remove the item

				//// leave this for now, i will be removing the div later on
				$(this).closest('[data-item-form]').addClass('hide');
			});

			///// This is used to close an item form box
			$document.on('click', '[data-item-close]', function()
			{
				// Show the root form
				self.showRootForm();

				// Close the item form box
				$(this).closest('[data-item-form]').addClass('hide');
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

			// Clean the input values when there are changes
			$document.on('change', 'input[type="text"]', function() {

				$(this).val($.trim($(this).val()));

			});

			// When the value of the root name input changes
			$document.on('keyup', formOpt.root.name, function() {

				// Update the root slug value
				self.slugifyValue($(this).val(), formOpt.root.slug);

				// Update the new menu item inputs
				self.updateNewItem();

			})

			// When the value of the root slug input changes
			$document.on('change', formOpt.root.slug, function() {

				// Clean the root slug value
				self.slugifyValue($(this).val(), formOpt.root.slug);

				// Update the new menu item inputs
				self.updateNewItem();

			});



			// Validate children items
			/*
			$document.on('change', 'input[data-children]', function()
			{
				alert('y');
			});
			*/




			/**
			 * Adds a new menu item.
			 *
			 */
			$document.on('click', formOpt.children.submit, function(e) {

				// Prevent the form from being submited
				e.preventDefault();

				// Check if form is validated
				if (self.validateInputs(formOpt.children))
				{
					// Hide the no children div
					$(options.noChildrenSelector).addClass('hide');

					// Generate the children slug
					var slug = $(formOpt.children.slug.input).val().slugify();

					// Prepare the new item data
					var data = {
						'name'       : $.trim($(formOpt.children.name.input).val()),
						'slug'       : slug,
						'type'       : $(formOpt.children.type.input).val(),
						'uri'        : $.trim($(formOpt.children.uri.input).val()),
						'visibility' : $(formOpt.children.visibility.input).val(),
						'secure'     : $(formOpt.children.secure.input).val(),
						'target'     : $(formOpt.children.target.input).val(),
						'class'      : $.trim($(formOpt.children.klass.input).val()),
						'enabled'    : $(formOpt.children.enabled.input).val()
					};

					// Append the new menu item
					self.TempoJs.append(data);

					// Add the item to the array
					options.persistedSlugs.push(slug);

					// Clean the new item inputs
					$(formOpt.children.name.input).val('');
					//$(formOpt.children.slug.input).val(base.generateChildrenSlug());
					$(formOpt.children.uri.input).val('');
					$(formOpt.children.klass.input).val('');

					// Close the modal window
					$('#create-child').modal('hide');

					return true;
				}

				return false;

			});

			// Removes a menu item
			$document.on('click', formOpt.itemRemove, function() {

				// Confirmation message
				var message = "Are you sure you want to delete this menu item?";

				// Confirm if the user wants to remove the item
				if (confirm(message) == true)
				{
					// Get the item selector
					var itemSelector = '.' + options.nestable.itemClass;

					// Get this item id
					var itemId = $(this).closest(itemSelector).data('id');

					// Get this item slug
					var itemSlug = $(this).closest(itemSelector).data('slug');

					// Remove the item from the array
					options.persistedSlugs.splice($.inArray(itemSlug, options.persistedSlugs), 1);

					// Get both data and item identifier
					var dataIdentifier = (typeof itemSlug == 'undefined' ? 'id' : 'slug');
					var itemIdentifier = (typeof itemSlug == 'undefined' ? itemId : itemSlug);

					// Find closest item
					var $item = $(itemSelector + '[data-' + dataIdentifier + '="' + itemIdentifier + '"]');
					var $list = $item.children(options.nestable.listNodeName);

					// Check if we have children
					if ($list.length > 0)
					{
						// Grab the list's children items and put them after this item
						$childItems = $list.children(options.nestable.itemNodeName);
						$childItems.insertAfter($item);
					}

					// Remove the item from the menu
					$item.remove();

					if ($(options.nestable.selector + ' > ol > li').length == 0)
					{
						$(options.noChildrenSelector).removeClass('hide');
					}

					// Close Bootstrap Modal
					$('.modal-backdrop').remove();

				}

			});

			// When the main form is submited
			$document.on('submit', self.$form, function(e) {

				// for now...
				e.preventDefault();

				// Append input to the form. It's values are JSON encoded..
				//return this.$form.append('<input type="hidden" name="' + this.opt.hierarchyInputName + '" value=\'' + window.JSON.stringify($(this.opt.nestable.selector).nestable('serialize')) + '\'>');

			});

		},

		/**
		 *
		 *
		 * @return void
		 */
		updateNewItem : function() {

			var self = this;

			var options = self.opt.form.children;

			// Generate a new slug based on the root menu slug
			var newSlug = self.getRootSlug() + ' ' + $(options.name.input).val();

			// Update the new item slug
			self.slugifyValue(newSlug, options.slug.input);

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
		slugifyValue : function(value, input) {

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

		showRootForm : function() {

			$('#root-details').removeClass('hide');

		},

		hideRootForm : function() {

			$('#root-details').addClass('hide');

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
