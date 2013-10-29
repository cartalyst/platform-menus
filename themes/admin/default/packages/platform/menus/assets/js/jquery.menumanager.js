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
 *  - Adding / Updating : Show pages list dropdown when the Item type is set to Page
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

		// Holds all the registered types
		types : {},

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
					rules : ['required_if:type=static'] // to implement
				},

				visibility : {
					input : '#child_visibility'
				},

				groups : {
					input : '#child_groups'
				},

				attributes : {
					id : {
						input : '#child_attribute_id',
					},

					klass : {
						input : '#child_attribute_class'
					},

					name : {
						input : '#child_attribute_name'
					},

					title : {
						input : '#child_attribute_title'
					},

					target : {
						input : '#child_attribute_target'
					}
				}

			}

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

			// Avoid scope issues
			var self = this;

			// Prepare TempoJS
			this.TempoJsMain = Tempo.prepare(this.opt.tempo.mainSelector, this.opt.tempo);
			this.TempoJsForms = Tempo.prepare(this.opt.tempo.formsSelector, this.opt.tempo);

			// Activate Nestable
			$(this.opt.nestable.selector).nestable(this.opt.nestable).on('change', function(event) {

				console.log(event.target.id);

				console.log(self);

				self.renderParentsDropdowns();

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

				// Prepare the inputs
				var parentId        = self.prepareInput('new-child', formOpt.children.parent.input).val();
				var nameInput       = self.prepareInput('new-child', formOpt.children.name.input);
				var slugInput       = self.prepareInput('new-child', formOpt.children.slug.input);
				var enabledInput    = self.prepareInput('new-child', formOpt.children.enabled.input);
				var typeInput       = self.prepareInput('new-child', formOpt.children.type.input);
				var secureInput     = self.prepareInput('new-child', formOpt.children.secure.input);
				var staticUriInput  = self.prepareInput('new-child', formOpt.children.static_uri.input);
				var visibilityInput = self.prepareInput('new-child', formOpt.children.visibility.input);
				var groupsInput     = self.prepareInput('new-child', formOpt.children.groups.input);
				var attrIdInput     = self.prepareInput('new-child', formOpt.children.attributes.id.input);
				var attrClassInput  = self.prepareInput('new-child', formOpt.children.attributes.klass.input);
				var attrNameInput   = self.prepareInput('new-child', formOpt.children.attributes.name.input);
				var attrTitleInput  = self.prepareInput('new-child', formOpt.children.attributes.title.input);
				var attrTargetInput = self.prepareInput('new-child', formOpt.children.attributes.target.input);

				// Generate the children slug
				var slug = slugInput.val().slugify();

				// Check if this is an unique slug
				if ( ! self.isUniqueSlug(slug))
				{
					alert('Unique slug, fix it...');
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


						'static_uri' : staticUriInput.val(),
						// need to add the dynamic type: the input name needs to be something like
						//
						//    "`type`_uri"
						//    (`type` will be the type value from the dropdown, this way it is more dynamic)
						//

						'visibility'       : visibilityInput.val(),
						'groups'           : groupsInput.val(),
						'attribute_id'     : attrIdInput.val(),
						'attribute_class'  : attrClassInput.val(),
						'attribute_name'   : attrNameInput.val(),
						'attribute_title'  : attrTitleInput.val(),
						'attribute_target' : attrTargetInput.val()

					};

					// Append the new menu item
					self.TempoJsMain.append(data);
					self.TempoJsForms.append(data);

					// Add the item to the array
					options.persistedSlugs.push(slug);

					// Clean the new form item inputs
					nameInput.val('');
					self.slugify($(formOpt.root.slug).val(), slugInput);
					uriInput.val('');
					attrIdInput.val('');
					attrClassInput.val('');
					attrNameInput.val('');
					attrTitleInput.val('');

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

				// Get the item form box
				var formBox = $('[data-item-form="' + formId + '"]');

				// Get the current slug
				var currentSlug = $('#' + formId + '_current-slug').val();

				// The current parent that this item belongs to
				var currentParentId = formBox.data('item-parent');

				// Id of the parent of this item
				var parentId = $('#' + formId + '_parent').val();

				// Get the new slug
				var slug = $('#' + formId + '_slug').val().slugify();

				// Check if this is an unique slug
				if ( ! self.isSameSlug(currentSlug, slug) & ! self.isUniqueSlug(slug))
				{
					alert('Unique slug, fix it...');
				}

				else
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
						alert('Move item..');

						// Update this item form box parent id
						formBox.data('item-parent', parentId);
					}

					// Update the li item name with the new item name,
					// just in case the item name gets updated.
					$('[data-item="' + formId + '"]').html($('#' + formId + '_name').val());

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

			this.opt.types[type] = name;

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

		/**
		 *
		 *
		 * @return void
		 */
		renderParentsDropdowns : function() {

			$('[data-parents]').html('<option value="0">-- Top Level --</option>' + this.convertToDropdown($(this.opt.nestable.selector + ' > ol'), 0));

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

						if (rule == 'required' && $($input).val() == '')
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
