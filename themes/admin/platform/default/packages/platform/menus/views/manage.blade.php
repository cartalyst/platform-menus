<style type="text/css">

.cf:after { visibility: hidden; display: block; font-size: 0; content: " "; clear: both; height: 0; }
* html .cf { zoom: 1; }
*:first-child+html .cf { zoom: 1; }

html { margin: 0; padding: 0; }
body { font-size: 100%; margin: 0; padding: 1.75em; font-family: 'Helvetica Neue', Arial, sans-serif; }

h1 { font-size: 1.75em; margin: 0 0 0.6em 0; }

a { color: #2996cc; }
a:hover { text-decoration: none; }

p { line-height: 1.5em; }
.small { color: #666; font-size: 0.875em; }
.large { font-size: 1.25em; }


#nestable-output{ width: 100%; height: 7em; font-size: 0.75em; line-height: 1.333333em; font-family: Consolas, monospace; padding: 5px; box-sizing: border-box; -moz-box-sizing: border-box; }



	</style>

		<link href="http://platform2.cy/platform\themes\admin\platform\default\extensions\platform\menus\assets\css/menus.css" rel="stylesheet">


</head>
<body>

	<menu id="nestable-menu">
		<button type="button" data-action="expand-all">Expand All</button>
		<button type="button" data-action="collapse-all">Collapse All</button>
	</menu>



	<div class="cf">
		<div class="dd" id="nestable">
			<ol class="dd-list">
			@foreach ($children as $child)
				@include('platform/menus::children', array('item' => $child))
			@endforeach
			</ol>
		</div>
	</div>



	<div class="cf">
		Name: <input name="children-name" id="children-name" value="" />
		<br>
		Slug: <input name="children-slug" id="children-slug" value="" />
		<p>
			<button name="children-add" id="children-add">Add Item</button>
		</p>
	</div>


	<p><strong>Serialised Output (per list)</strong></p>

	<textarea id="nestable-output"></textarea>




<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="http://dbushell.github.com/Nestable/jquery.nestable.js"></script>
<script>

$(document).ready(function()
{
	(function($)
	{
		/*
		 * TODO:
		 *	- Prepend the Menu slug to the children slug
		 *  - Figure some way to not allow the same slug more than once ...
		 *  - Refactor!?
		 *
		 */
		jQuery.MenuManager = function(options)
		{
			// to implement
			var currentSlugs = [];

			// Plugin default option values
			var option = {
				menuSelector         : '#nestable',
				childrenNameSelector : '#children-name',
				childrenSlugSelector : '#children-slug',
				childrenAddSelector  : '#children-add'
			};

			// Extend the default options with the
			// provided options.
    		$.extend(option, options);

			// Adding new children
			$(option.childrenNameSelector).keyup(function(){
				$(option.childrenSlugSelector).val(slugify($(this).val()));
			});
			$(option.childrenAddSelector).on('click', function()
			{
				name = $(option.childrenNameSelector).val();
				slug = slugify($(option.childrenSlugSelector).val());

				if (name != '' && slug != '')
				{
					html = '<li class="dd-item" data-slug="' + slug + '"><div class="dd-handle">' + name + '</div></li>';
					$(option.menuSelector + ' > ol').append(html);

					// not working ....
					updateOutput($(option.menuSelector).data('output', $(option.menuSelector + '-output')));
				}
			});
			// ------


			function updateOutput(e)
			{
				var list   = e.length ? e : $(e.target),
					output = list.data('output');
				if (window.JSON) {
					output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
				} else {
					output.val('JSON browser support required for this demo.');
				}
			}

			/**
			 * Converts a String to a Slug.
			 *
			 * @param  string
			 * @param  string
			 * @return string
			 */
			function slugify(string, separator)
			{
				// Make sure we have a slug separator
				separator = (typeof separator === 'undefined' ? '-' : separator);

				// Convert string to lowercase and
				// remove any spaces.
				string = string.toLowerCase().replace(/^\s+|\s+$/g, '');

				// Remove accents
				var from = 'ĺěščřžýťňďàáäâèéëêìíïîòóöôùůúüûñç·/_,:;';
				var to   = 'lescrzytndaaaaeeeeiiiioooouuuuunc------';
				for (var i=0, l=from.length ; i<l ; i++) {
					string = string.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
				}

				// Return the slugified string
				return string.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
					.replace(/\s+/g, separator) // collapse whitespace and replace by _
					.replace(/-+/g, separator) // collapse dashes
					.replace(new RegExp(separator + '+$'), '') // Trim separator from start
					.replace(new RegExp('^' + separator + '+'), ''); // Trim separator from end
			}


			// activate Nestable for list 1
			$('#nestable').nestable({maxDepth : 100}).on('change', updateOutput);

			// output initial serialised data
			updateOutput($('#nestable').data('output', $('#nestable-output')));
		}
	})(jQuery);

	$.MenuManager();




	$('#nestable-menu').on('click', function(e)
	{
		var target = $(e.target),
			action = target.data('action');
		if (action === 'expand-all') {
			$('.dd').nestable('expandAll');
		}
		if (action === 'collapse-all') {
			$('.dd').nestable('collapseAll');
		}
	});
});
</script>
