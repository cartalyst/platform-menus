	(function($)
	{
		jQuery.MenuManager = function(options)
		{
			// Default option values
			var option = {
				// Holds all the existing menu slugs
				persistedSlugs : [],

				// Slug separator
				slugSeparator     : '-',

				menuSelector      : '#nestable', // ...

				// Root
				rootNameSelector  : '#root-name',
				rootSlugSelector  : '#root-slug',
				//rootAddSelector   : '#root-save'

				// Children
				childNameSelector : '#children-name',
				childSlugSelector : '#children-slug',
				childAddSelector  : '#children-add'

			};

			// Extend the default options with the
			// provided options.
			$.extend(option, options);


			/**
			 * 	R O O T
			 *
			 */
			$(option.rootNameSelector).keyup(function(){
				// Update the root slug
				$(option.rootSlugSelector).val(generateSlug($(this).val()));

				// Get the child menu item name value
				childNameValue = $(option.childNameSelector).val();

				// Update the children slug
				if (childNameValue.length == 0)
				{
					$(option.childSlugSelector).val(generateChildrenSlug(childNameValue));
				}
			});



			/**
			 *  R E M O V E   I T E M S
			 *
			 */
			$('.remove').live('click', function(){
				// Get this item slug
				itemSlug = $(this).parent().parent().data('slug');

				// Remove the item from the array
				option.persistedSlugs.splice($.inArray(itemSlug, option.persistedSlugs), 1);

				// Remove the item from the menu
				$('.dd-item[data-slug="' + itemSlug + '"]').remove();

				//
				updateOutput();
			});


			/**
			 *  C H I L D R E N
			 *
			 */
			// generate the children slug
			$(option.childNameSelector).keyup(function(){
				$(option.childSlugSelector).val(generateChildrenSlug($(this).val()));
			});

			// Adding new children
			$(option.childAddSelector).on('click', function(e)
			{
				// Prevent the form from being submited
				e.preventDefault();

				//
				name = $(option.childNameSelector).val();
				slug = slugify($(option.childSlugSelector).val());

				// Make sure that both child name and slug
				// are not empty.
				if (name != '' && slug != '')
				{
					if (($.inArray(slug, option.persistedSlugs) > -1))
					{
						alert('item with this slug already exists');
						// show the error...
					}
					else
					{
						// remove the error...

						// Add the children...
						// ### find another clean way to do this
						html = '<li class="dd-item dd3-item" data-slug="' + slug + '">';
							html += '<div class="dd-handle dd3-handle">Drag</div>';
							html += '<div class="dd3-content">';
								html += '<div class="remove" style="float: right;">x</div>';
								html += name;
							html += '</div>';
						html += '</li>';

						$(option.menuSelector + ' > ol').append(html);

						// Add the item to the array
						option.persistedSlugs.push(slug);
					}

					//
					updateOutput();
				}
			});


			// Activate Nestable
			$(option.menuSelector).nestable({maxDepth : 100}).on('change', updateOutput);

			// Generate the initial children slug
			$(option.childSlugSelector).val(generateChildrenSlug());

			// output initial serialised data
			updateOutput();




			// we need to use this function when we post the data to the server
			// modify it a bit
			function updateOutput()
			{
				e = $(option.menuSelector).data('output', $(option.menuSelector + '-output'))

				var list   = e.length ? e : $(e.target),
					output = list.data('output');
				if (window.JSON) {
					output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
				} else {
					output.val('JSON browser support required for this demo.');
				}
			}



			/**
			 * Returns the current `Root item` slug.
			 *
			 * @return string
			 */
			function getRootSlug()
			{
				return $(option.rootSlugSelector).val() + option.slugSeparator;
			}

			/**
			 * Generates a slug.
			 *
			 * @param  string
			 * @return string
			 */
			function generateSlug(string)
			{
				// Make sure we have a string
				string = typeof string !== 'undefined' ? string : '';

				// Return the slugified string
				return slugify(string);
			}

			/**
			 * Generates a new children slug based
			 * on the root item slug.
			 *
			 * @param  string
			 * @return string
			 */
			function generateChildrenSlug(string)
			{
				// Make sure we have a string
				string = typeof string !== 'undefined' ? string : '';

				// Generate the slug and return it
				return getRootSlug() + generateSlug(string);
			}

			/**
			 * Converts a String to a Slug.
			 *
			 * @param  string
			 * @param  string
			 * @return string
			 */
			function slugify(string)
			{
				// Make sure we have a slug separator
				separator = option.slugSeparator;

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
			}
		}
	})(jQuery);
