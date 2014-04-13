<script type="text/template" id="form-template">

<div class="well well-md item-box-white-bg hide" data-item-form="<%= slug %>" data-item-parent="<%= parent_id %>">

	<input type="hidden" id="<%= slug %>_current-slug" value="<%= slug %>">

	<h4>
		{{{ trans('platform/menus::form.update.legend') }}}

		<span class="pull-right"><small class="item-box-close" data-item-close="<%= slug %>">&times;</small></span>
	</h4>

	<p>{{{ trans('platform/menus::form.update.description') }}}</p>

	{{-- Item details --}}
	<div class="well well-md item-box-borderless">

		<fieldset>

			<legend>Item details</legend>

			{{-- Name --}}
			<div class="form-group">
				<label class="control-label" for="<%= slug %>_name">{{{ trans('platform/menus::form.name') }}}</label>

				<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.name_help') }}}"></i>

				<input data-item-form="<%= slug %>" type="text" name="children[<%= slug %>][name]" id="<%= slug %>_name" class="form-control" value="<%= name %>">
			</div>

			{{-- Slug --}}
			<div class="form-group">
				<label class="control-label" for="<%= slug %>_slug">{{{ trans('platform/menus::form.slug') }}}</label>

				<i class="fa fa-info-sign" data-toggle="popover" data-content="{{{ trans('platform/menus::form.slug_help') }}}"></i>

				<input data-item-form="<%= slug %>" type="text" name="children[<%= slug %>][slug]" id="<%= slug %>_slug" class="form-control" value="<%= slug %>">
			</div>

			{{-- Enabled --}}
			<div class="form-group">
				<label class="control-label" for="<%= slug %>_enabled">{{{ trans('platform/menus::form.enabled') }}}</label>
				<div class="controls">
					<select data-item-form="<%= slug %>" name="children[<%= slug %>][enabled]" id="<%= slug %>_enabled" class="form-control">
						<option value="1"<%= enabled == '1' ? ' selected="selected"' : null %>>{{{ trans('general.enabled') }}}</option>
						<option value="0"<%= enabled == '0' ? ' selected="selected"' : null %>>{{{ trans('general.disabled') }}}</option>
					</select>
				</div>
			</div>

			{{-- Parent --}}
			<div class="form-group">
				<label class="control-label" for="<%= slug %>_parent">{{{ trans('platform/menus::form.parent') }}}</label>

				<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.parent_help') }}}"></i>

				<div class="controls">
					<select data-item-form="<%= slug %>" data-parents id="<%= slug %>_parent" class="form-control"></select>
				</div>
			</div>

		</fieldset>

	</div>

	{{-- Item URL --}}
	<div class="well well-md item-box-borderless">

		<fieldset>

			<legend>Item URL</legend>

			<div class="row">

				<div class="col-md-6">

					{{-- Item Type --}}
					<div class="form-group">
						<label class="control-label" for="<%= slug %>_type">{{{ trans('platform/menus::form.type') }}}</label>

						<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.type_help') }}}"></i>

						<div class="controls">
							<select data-item-url-type="<%= slug %>" data-item-form="<%= slug %>" name="children[<%= slug %>][type]" id="<%= slug %>_type" class="form-control">
								@foreach ($types as $type)
								<option value="{{ $type->getIdentifier() }}"<%= type == '{{ $type->getIdentifier() }}' ? ' selected="selected"' : null %>>{{ $type->getName() }}</option>
								@endforeach
							</select>
						</div>
					</div>

				</div>

				<div class="col-md-6">

					{{-- Secure --}}
					<div class="form-group">
						<label class="control-label" for="<%= slug %>_secure">{{{ trans('platform/menus::form.secure') }}}</label>
						<div class="controls">
							<select data-item-form="<%= slug %>" name="children[<%= slug %>][secure]" id="<%= slug %>_secure" class="form-control">
								<option value="1"<%= secure == '1' ? ' selected="selected"' : null %>>{{{ trans('general.yes') }}}</option>
								<option value="0"<%= secure == '0' ? ' selected="selected"' : null %>>{{{ trans('general.no') }}}</option>
							</select>
						</div>
					</div>

				</div>

			</div>

			{{-- Generate the types inputs --}}
			@foreach ($types as $type)
				{{ $type->getTemplateHtml() }}
			@endforeach

		</fieldset>

	</div>

	<button type="button" class="btn btn-sm btn-info" data-toggle-options="<%= slug %>">{{{ trans('platform/menus::button.more_options') }}}</button>

	<span class="pull-right">
		<button class="btn btn-sm btn-success" data-item-update="<%= slug %>">{{{ trans('button.update') }}}</button>

		<button class="btn btn-sm btn-danger" data-item-remove="<%= slug %>">{{{ trans('button.remove') }}}</button>
	</span>

	{{-- Options --}}
	<div class="hide" style="padding-top: 20px;" data-options>

		<div class="well well-md item-box-borderless">

			<fieldset>

				<legend>{{{ trans('platform/menus::form.visibility') }}}</legend>

				{{-- Visibility --}}
				<div class="form-group">
					<label class="control-label" for="<%= slug %>_visibility">{{{ trans('platform/menus::form.visibility') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.visibility_help') }}}"></i>

					<div class="controls">
						<select data-item-form="<%= slug %>" data-item-visibility="<%= slug %>" name="children[<%= slug %>][visibility]" id="<%= slug %>_visibility" class="form-control">
							<option value="always"<%= visibility == 'always' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::form.visibilities.always') }}}</option>
							<option value="logged_in"<%= visibility == 'logged_in' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::form.visibilities.logged_in') }}}</option>
							<option value="logged_out"<%= visibility == 'logged_out' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::form.visibilities.logged_out') }}}</option>
							<option value="admin"<%= visibility == 'admin' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::form.visibilities.admin') }}}</option>
						</select>
					</div>
				</div>

				{{-- Groups --}}
				<div class="form-group<%= _.indexOf(['always', 'logged_out'], visibility) > -1 ? ' hide' : null %>" data-item-groups="<%= slug %>">
					<label class="control-label" for="<%= slug %>_groups">{{{ trans('platform/menus::form.groups') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.groups_help') }}}"></i>

					<div class="controls">
						<select data-item-form="<%= slug %>" name="children[<%= slug %>][groups][]" id="<%= slug %>_groups" class="form-control" multiple="true">
							@foreach ($groups as $group)
							<option value="{{{ $group->id }}}"<%= _.indexOf(groups, '{{ $group->id }}') > -1 ? ' selected="selected"' : null %>>{{{ $group->name }}}</option>
							@endforeach
						</select>
					</div>
				</div>

			</fieldset>

		</div>

		{{-- Attributes --}}
		<div class="well well-md item-box-borderless">

			<fieldset>

				<legend>Attributes</legend>

				{{-- Class --}}
				<div class="form-group">
					<label class="control-label" for="<%= slug %>_class">{{{ trans('platform/menus::form.class') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.class_help') }}}"></i>

					<input data-item-form="<%= slug %>" type="text" name="children[<%= slug %>][class]" id="<%= slug %>_class" class="form-control" value="<%= klass %>">
				</div>

				{{-- Target --}}
				<div class="form-group">
					<label class="control-label" for="<%= slug %>_target">{{{ trans('platform/menus::form.target') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.target_help') }}}"></i>

					<div class="controls">
						<select data-item-form="<%= slug %>" name="children[<%= slug %>][target]" id="<%= slug %>_target" class="form-control">
							<option value="self"<%= target == 'self' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::form.targets.self') }}}</option>
							<option value="new_children"<%= target == 'new_children' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::form.targets.blank') }}}</option>
							<option value="parent_frame"<%= target == 'parent_frame' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::form.targets.parent') }}}</option>
							<option value="top_frame"<%= target == 'top_frame' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::form.targets.top') }}}</option>
						</select>
					</div>
				</div>

			</fieldset>

		</div>

		{{-- Regular Expression --}}
		<div class="well well-md item-box-borderless">

			<fieldset>

				<legend>Regular Expression</legend>

				{{-- Regular Expression --}}
				<div class="form-group">
					<label class="control-label" for="<%= slug %>_regex">{{{ trans('platform/menus::form.regex') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.regex_help') }}}"></i>

					<input data-item-form="<%= slug %>" type="text" name="children[<%= slug %>][regex]" id="<%= slug %>_regex" class="form-control" value="<%= regex %>">
				</div>

			</fieldset>

		</div>

	</div>

</div>

</script>
