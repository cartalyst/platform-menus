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
 * @copyright  (c) 2011-2014, Cartalyst LLC
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

		// Holds all of the existing menu slugs
		persistedSlugs : [],

		// Holds all the registered types
		types : {},

		// Slug separator
		slugSeparator : '-',

		// Underscore template elements
		templates : {

			item : '#item-template',
			form : '#form-template'

		},

		// Sortable settings
		sortable : {

			selector          : '#sortable > ol',
			containerSelector : 'ol',
			itemSelector      : 'li'

		},

		// Form elements
		form : {

			// This is the name of the input that is submitted with the
			// children items, this contains the children hierarchy.
			tree : 'menu-tree',

			group : '.form-group',

			errorClass : 'has-error',

			// Root elements
			root : {

				name : '#menu-name',
				slug : '#menu-slug',

			},

			// Children elements
			children : {

				name : {
					input: '#child_name',
					rules :	['required']
				},

				slug : {
					input : '#child_slug',
					rules :	['required']
				},

				enabled : {
					input : '#child_enabled'
				},

				parent : {
					input : '#child_parent'
				},

				type : {
					input : '#child_type'
				},

				secure : {
					input : '#child_secure'
				},

				static_uri : {
					input : '#child_static_uri',
					rules : ['required_if:type:static']
				},

				visibility : {
					input : '#child_visibility'
				},

				groups : {
					input : '#child_groups'
				},

				klass : {
					input : '#child_class'
				},

				target : {
					input : '#child_target'
				},

				regex : {
					input : '#child_regex'
				}

			}

		},

		// Are we saving the menu?
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

			// Avoid scope issues
			var self = this;

			// Activate Sortable
			$(this.opt.sortable.selector).sortable({

				placeholder: '<li class="placeholder"></li>',
				handle: 'div.item-handle',
				onDrop: function (item, container, _super) {

					// Get the parent id
					var parentId = item.parent('ol').parent('li').data('item-id');

					// Get the item id
					var itemId = item.data('item-id');

					// Make sure we have a proper parent id
					if (parentId == null)
					{
						var parentId = 0;
					}

					// Update the parent id on the item form box
					$('[data-item-form="' + itemId + '"]').data('item-parent', parentId);

					// Refresh the parent dropdowns
					self.renderParentsDropdowns();

					// Make sure that the parent dropdown has the correct value selected
					$('#' + itemId + '_parent').val(parentId);

					// We have unsaved changes
					self.opt.unsavedChanges = true;

					_super(item, container);

				},
				serialize: function (parent, children, isContainer) {

					var result = $.extend({}, { id : parent.data('item-id') });

					if (isContainer)
					{
						return children;
					}
					else if (children[0])
					{
						result.children = children;
					}

					return result;

				}

			});

			// Initialize the event listeners
			this.events();

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
			self.slugify($(formOpt.root.slug).val(), self.prepareInput('new-child', formOpt.children.slug.input));

			// Set a bind to check if we have unsaved changes when
			// we are about to leave the menu manager page.
			$(window).bind('beforeunload', function() {

				if (options.unsavedChanges & ! options.isSaving)
				{
					return 'You have unsaved changes.';
				}

			});

			// Pre-render the parents dropdowns
			self.renderParentsDropdowns();

			// Clean the input values when there are changes
			$document.on('change', 'input[type="text"]', function() {

				// Clean the input first
				$(this).val($.trim($(this).val()));

				// Only trigger if we updated the item slug
				if ($(this).attr('id').indexOf('_slug') > -1)
				{
					self.slugify($(this).val(), this);
				}

			});

			// When menu children data get's updated
			$document.on('keyup', 'input[type="text"]', function() {

				// Get the form box id
				var itemId = $(this).data('item-form');

				// Only trigger if we updated the item name
				if ($(this).attr('id').indexOf('_name') > -1)
				{
					// Get the root slug
					var rootSlug = self.getRootSlug();

					// Get the item name value
					var name = $(this).val();

					// Make sure we have a proper slug
					self.slugify(rootSlug + ' ' + name, '#' + itemId + '_slug');
				}

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
			 * When an item url type changes.
			 *
			 * @return void
			 */
			$document.on('change', '[data-item-url-type]', function() {

				var itemId = $(this).data('item-url-type');

				var selectedOption = $(this).val();

				var itemBox = $('[data-item-form="' + itemId + '"]');

				itemBox.find('[data-item-type]').addClass('hide');

				itemBox.find('[data-item-type="' + selectedOption + '"]').removeClass('hide');

			});


			/**
			 * Shows a menu item form box.
			 *
			 * @return void
			 */
			$document.on('click', '[data-item]', function(e) {

				// Prevent the form from being submitted
				e.preventDefault();

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
			 * When an item visibility changes.
			 *
			 * @return void
			 */
			$document.on('change', '[data-item-visibility]', function(e) {

				var item = $(this).data('item-visibility');

				var selectedOption = $(this).val();

				if ($.inArray(selectedOption, ['logged_in', 'admin']) > -1)
				{
					$('[data-item-groups="' + item + '"]').removeClass('hide');
				}
				else
				{
					$('[data-item-groups="' + item + '"]').addClass('hide');
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
			 * Adds a new menu item.
			 *
			 * @return void
			 */
			$document.on('click', '[data-item-create]', function(e) {

				// Prevent the form from being submitted
				e.preventDefault();

				// Prepare the inputs
				var nameInput       = self.prepareInput('new-child', formOpt.children.name.input);
				var slugInput       = self.prepareInput('new-child', formOpt.children.slug.input);
				var enabledInput    = self.prepareInput('new-child', formOpt.children.enabled.input);
				var parentInput     = self.prepareInput('new-child', formOpt.children.parent.input);
				var typeInput       = self.prepareInput('new-child', formOpt.children.type.input);
				var secureInput     = self.prepareInput('new-child', formOpt.children.secure.input);
				var visibilityInput = self.prepareInput('new-child', formOpt.children.visibility.input);
				var groupsInput     = self.prepareInput('new-child', formOpt.children.groups.input);
				var classInput      = self.prepareInput('new-child', formOpt.children.klass.input);
				var targetInput     = self.prepareInput('new-child', formOpt.children.target.input);
				var regexInput      = self.prepareInput('new-child', formOpt.children.regex.input);

				// Get the parent id
				var parentId = parentInput.val();

				// Generate the children slug
				var slug = slugInput.val().slugify();

				// Check if this is an unique slug
				if ( ! self.isUniqueSlug(slug))
				{
					alert('The slug [' + slug + '] is already taken!');
				}

				// Check if the form is valid
				else if (self.validateInputs('new-child', formOpt.children))
				{
					// Prepare the new item data
					var data = {

						'parent_id' : parentId,
						'name'      : nameInput.val(),
						'slug'      : slug,
						'enabled'   : enabledInput.val(),

						'type'   : typeInput.val(),
						'secure' : secureInput.val(),

						'visibility' : visibilityInput.val(),
						'groups'     : groupsInput.val(),

						'klass'  : classInput.val(),
						'target' : targetInput.val(),

						'regex' : regexInput.val()

					};

					// Attach the uri to the data for the template
					$.each(options.types, function(id, name) {

						data[id + '_uri'] = self.prepareInput('new-child', '#child_' + id + '_uri').val();

					});

					// Append the new menu item
					$(options.sortable.selector).append(_.template($(options.templates.item).html(), data));
					$('[data-forms]').append(_.template($(options.templates.form).html(), data));

					// Add the item to the array
					options.persistedSlugs.push(slug);

					// Clean the new form item inputs
					nameInput.val('');
					self.slugify($(formOpt.root.slug).val(), slugInput);
					enabledInput.val('1');
					parentInput.val('0');
					typeInput.val('static');
					secureInput.val('0');
					classInput.val('');
					targetInput.val('self');
					regexInput.val('');
					visibilityInput.val('always');
					$('[data-item-groups="new-child"]').addClass('hide');
					groupsInput.val('');

					// Get the new item form box
					var newItemForm = $('[data-item-form="new-child"]');

					// Reset the types
					$.each(options.types, function(id, name) {

						newItemForm.find('[data-item-type="' + id + '"]').addClass('hide');

					});

					// Make sure the static is the default
					self.prepareInput('new-child', formOpt.children.static_uri.input).val('');
					newItemForm.find('[data-item-type="static"]').removeClass('hide');

					// Hide the more options area
					newItemForm.find('[data-options]').addClass('hide');

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

					// Remove the error class
					$('.' + formOpt.errorClass).removeClass(formOpt.errorClass);

					// Hide the add new item form box
					newItemForm.addClass('hide');

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

				// Get the item form box
				var formBox = $('[data-item-form="' + formId + '"]');

				// Get the current slug
				var currentSlug = $('#' + formId + '_current-slug').val();

				// Get the item status
				var enabled = $('#' + formId + '_enabled').val();

				// The current parent that this item belongs to
				var currentParentId = formBox.data('item-parent');

				// Id of the parent of this item
				var parentId = $('#' + formId + '_parent').val();

				// Get the new slug
				var slug = $('#' + formId + '_slug').val().slugify();

				// Check if this is an unique slug
				if ( ! self.isSameSlug(currentSlug, slug) & ! self.isUniqueSlug(slug))
				{
					alert('The slug [' + slug + '] is already taken!');
				}

				// Check if the form is valid
				else if (self.validateInputs(formId, formOpt.children))
				{
					// Remove the item from the array, because we
					// might have changed the slug.
					options.persistedSlugs.splice($.inArray(currentSlug, options.persistedSlugs), 1);

					// Add the item slug to the array
					options.persistedSlugs.push(slug);

					// Update the current slug input value
					$('#' + formId + '_current-slug').val(slug);

					// Show the root form
					self.showRootForm();

					// We have unsaved changes
					options.unsavedChanges = true;

					// Hide the form item box
					formBox.addClass('hide');

					// Have we changed the parent of the item?
					if (currentParentId != parentId)
					{
						// Make sure we are moving to a valid parent
						if (formId != parentId)
						{
							// Are we moving to the root level?
							if (parentId == 0)
							{
								var moveTo = $(options.sortable.selector);
							}
							else
							{
								var moveTo = $('[data-item-id="' + parentId + '"] > ol');
							}

							// Move the item to the correct destination
							$('[data-item-id="' + formId + '"]').appendTo(moveTo);

							// Update this item form box parent id
							formBox.data('item-parent', parentId);
						}
					}

					// Update the li item name with the new item name,
					// just in case the item name gets updated.
					$('[data-item-name="' + formId + '"]').html($('#' + formId + '_name').val());

					// Show/hide the status handle
					if (enabled == 0)
					{
						$('[data-item-status="' + formId + '"]').removeClass('hide');
					}
					else
					{
						$('[data-item-status="' + formId + '"]').addClass('hide');
					}

					// Refresh the parents dropdowns
					self.renderParentsDropdowns();
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
					var list = item.children(options.sortable.containerSelector);

					// Check if we have children
					if (list.length > 0)
					{
						// Grab the list's children items and put them after this item
						var childItems = list.children(options.sortable.itemSelector);
						childItems.insertAfter(item);
					}

					// Remove the item from the array
					options.persistedSlugs.splice($.inArray(itemId, options.persistedSlugs), 1);

					// Remove the item from the menu
					item.remove();

					// Check if we have children
					if ($(options.sortable.selector + ' > li').length == 0)
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
				return $(self.$form).append('<input type="hidden" name="' + options.form.tree + '" value=\'' + window.JSON.stringify($(self.opt.sortable.selector).sortable("serialize").get()) + '\'>');

			});

		},

		/**
		 * Set the persisted slugs.
		 *
		 * @param  array  slugs
		 * @return void
		 */
		setPersistedSlugs : function(slugs) {

			this.opt.persistedSlugs = slugs;

		},

		/**
		 * Register a new type.
		 *
		 * @param  string  name
		 * @param  string  type
		 * @return void
		 */
		registerType : function(name, type) {

			// Register the type
			this.opt.types[type] = name;

			// Set the validation rules for this type
			this.opt.form.children[type + '_uri'] = {
				input : '#child_' + type + '_uri',
				rules : ['required_if:type:' + type]
			};

		},

		/**
		 * Return a list of registered types.
		 *
		 * @return array
		 */
		getTypes : function() {

			return this.opt.types;

		},

		/**
		 *
		 *
		 * @param  float  level
		 * @return float
		 */
		spacers : function(level) {

			var spacers = '';

			for(var j=0; j < level * 3; j++)
			{
				spacers += '&nbsp;';
			}

			return spacers;

		},

		/**
		 * Converts an OL into a HTML Dropdown menu.
		 *
		 * @param  object  OL
		 * @param  float   level
		 * @return string
		 */
		convertToDropdown : function(OL, level) {

			var self = this;

			var dropdown = '';

			OL.children('li').each(function () {

				var id = $(this).data('item-id');

				var text = self.spacers(level) + $(this).find('[data-item-name="' + id + '"]').text();

				dropdown += '<option value="' + id + '">' + text + '</option>';

				var children = $(this).children('ol');

				if (children.length > 0)
				{
					dropdown += self.convertToDropdown(children, level + 1);
				}

			});

			return dropdown;

		},

		/**
		 *
		 *
		 * @return void
		 */
		renderParentsDropdowns : function() {

			$('[data-parents]').html('<option value="0">-- Top Level --</option>' + this.convertToDropdown($(this.opt.sortable.selector), 0));

		},

		/**
		 * Prepare the input object.
		 *
		 * @param  string  id
		 * @param  string  name
		 * @return object
		 */
		prepareInput : function(id, name) {

			return $(name.replace('child', id));

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

			return $.inArray(slug, this.opt.persistedSlugs) > -1 ? false : true;

		},

		/**
		 *
		 *
		 * @return void
		 */
		updateNewFormInputs : function() {

			var self = this;

			// Get the children inputs options
			var options = self.opt.form.children;

			// Generate a new slug based on the root menu slug
			var newSlug = self.getRootSlug() + ' ' + self.prepareInput('new-child', options.name.input).val();

			// Update the new item slug
			self.slugify(newSlug, self.prepareInput('new-child', options.slug.input));

		},

		/**
		 * Returns the root slug.
		 *
		 * @return string
		 */
		getRootSlug : function() {

			return $(this.opt.form.root.slug).val();

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
		 * @param  string  id
		 * @param  array   inputs
		 * @return bool
		 */
		validateInputs : function(id, inputs) {

			var self = this;

			var failedInputs = [];

			// Loop through the inputs
			$.each(inputs, function(input, value) {

				// Does this input have rules?
				if (typeof value.rules !== 'undefined')
				{
					// Loop through the rules
					$.each(value.rules, function(key, rule) {

						var $input = value.input.replace('child', id);

						if (rule.indexOf('required_if') != -1)
						{
							var rule = rule.split(':');

							var requiredInputValue = self.prepareInput(id, self.opt.form.children[rule[1]]['input']).val();

							if (requiredInputValue == rule[2] & $($input).val() == '')
							{
								self.showError($input);

								failedInputs.push($input);
							}
						}

						else if (rule == 'required' && $($input).val() == '')
						{
							self.showError($input);

							failedInputs.push($input)
						}

						else
						{
							self.hideError($input);
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

		/**
		 * Shows the error on the input
		 *
		 * @param  object  input
		 * @return void
		 */
		showError : function(input) {

			$(input).closest(this.opt.form.group).addClass(this.opt.form.errorClass);

		},

		/**
		 * Hides the error on the input.
		 *
		 * @param  object  input
		 * @return void
		 */
		hideError : function(input) {

			$(input).closest(this.opt.form.group).removeClass(this.opt.form.errorClass);

		}

	};

	$.menumanager = function(menu, options) {

		return new MenuManager(menu, options);

	};

})(jQuery, window, document);
