<script type="text/template" id="form-template">

<div class="well well-md item-box-white-bg hide" data-item-form="<%= slug %>" data-item-parent="<%= parent_id %>">

	<input type="hidden" id="<%= slug %>_current-slug" value="<%= slug %>">

	<h4>
		{{{ trans('platform/menus::model.update.legend') }}}

		<span class="pull-right"><small class="item-box-close" data-item-close="<%= slug %>">&times;</small></span>
	</h4>

	<p>{{{ trans('platform/menus::model.update.description') }}}</p>

	{{-- Item details --}}
	<div class="well well-md item-box-borderless">

		<fieldset>

			<legend>Item details</legend>

			{{-- Name --}}
			<div class="form-group">
				<label class="control-label" for="<%= slug %>_name">{{{ trans('platform/menus::model.name') }}}</label>

				<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.name_help') }}}"></i>

				<input data-item-form="<%= slug %>" type="text" name="children[<%= slug %>][name]" id="<%= slug %>_name" class="form-control" value="<%= name %>">
			</div>

			{{-- Slug --}}
			<div class="form-group">
				<label class="control-label" for="<%= slug %>_slug">{{{ trans('platform/menus::model.slug') }}}</label>

				<i class="fa fa-info-sign" data-toggle="popover" data-content="{{{ trans('platform/menus::model.slug_help') }}}"></i>

				<input data-item-form="<%= slug %>" type="text" name="children[<%= slug %>][slug]" id="<%= slug %>_slug" class="form-control" value="<%= slug %>">
			</div>

			{{-- Enabled --}}
			<div class="form-group">
				<label class="control-label" for="<%= slug %>_enabled">{{{ trans('platform/menus::model.enabled') }}}</label>
				<div class="controls">
					<select data-item-form="<%= slug %>" name="children[<%= slug %>][enabled]" id="<%= slug %>_enabled" class="form-control">
						<option value="1"<%= enabled == '1' ? ' selected="selected"' : null %>>{{{ trans('common.enabled') }}}</option>
						<option value="0"<%= enabled == '0' ? ' selected="selected"' : null %>>{{{ trans('common.disabled') }}}</option>
					</select>
				</div>
			</div>

			{{-- Parent --}}
			<div class="form-group">
				<label class="control-label" for="<%= slug %>_parent">{{{ trans('platform/menus::model.parent') }}}</label>

				<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.parent_help') }}}"></i>

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
						<label class="control-label" for="<%= slug %>_type">{{{ trans('platform/menus::model.type') }}}</label>

						<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.type_help') }}}"></i>

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
						<label class="control-label" for="<%= slug %>_secure">{{{ trans('platform/menus::model.secure') }}}</label>
						<div class="controls">
							<select data-item-form="<%= slug %>" name="children[<%= slug %>][secure]" id="<%= slug %>_secure" class="form-control">
								<option value="1"<%= secure == '1' ? ' selected="selected"' : null %>>{{{ trans('common.yes') }}}</option>
								<option value="0"<%= secure == '0' ? ' selected="selected"' : null %>>{{{ trans('common.no') }}}</option>
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

	<button type="button" class="btn btn-sm btn-info" data-toggle-options="<%= slug %>">{{{ trans('platform/menus::action.more_options') }}}</button>

	<span class="pull-right">
		<button class="btn btn-sm btn-success" data-item-update="<%= slug %>">{{{ trans('action.update') }}}</button>

		<button class="btn btn-sm btn-danger" data-item-remove="<%= slug %>">{{{ trans('action.remove') }}}</button>
	</span>

	{{-- Options --}}
	<div class="hide" style="padding-top: 20px;" data-options>

		<div class="well well-md item-box-borderless">

			<fieldset>

				<legend>{{{ trans('platform/menus::model.visibility') }}}</legend>

				{{-- Visibility --}}
				<div class="form-group">
					<label class="control-label" for="<%= slug %>_visibility">{{{ trans('platform/menus::model.visibility') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.visibility_help') }}}"></i>

					<div class="controls">
						<select data-item-form="<%= slug %>" data-item-visibility="<%= slug %>" name="children[<%= slug %>][visibility]" id="<%= slug %>_visibility" class="form-control">
							<option value="always"<%= visibility == 'always' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::model.visibilities.always') }}}</option>
							<option value="logged_in"<%= visibility == 'logged_in' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::model.visibilities.logged_in') }}}</option>
							<option value="logged_out"<%= visibility == 'logged_out' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::model.visibilities.logged_out') }}}</option>
							<option value="admin"<%= visibility == 'admin' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::model.visibilities.admin') }}}</option>
						</select>
					</div>
				</div>

				{{-- Groups --}}
				<div class="form-group<%= _.indexOf(['always', 'logged_out'], visibility) > -1 ? ' hide' : null %>" data-item-roles="<%= slug %>">
					<label class="control-label" for="<%= slug %>_roles">{{{ trans('platform/menus::model.roles') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.roles_help') }}}"></i>

					<div class="controls">
						<select data-item-form="<%= slug %>" name="children[<%= slug %>][roles][]" id="<%= slug %>_roles" class="form-control" multiple="true">
							@foreach ($roles as $role)
							<option value="{{{ $role->id }}}"<%= _.indexOf(roles, '{{ $role->id }}') > -1 ? ' selected="selected"' : null %>>{{{ $role->name }}}</option>
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
					<label class="control-label" for="<%= slug %>_class">{{{ trans('platform/menus::model.class') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.class_help') }}}"></i>

					<input data-item-form="<%= slug %>" type="text" name="children[<%= slug %>][class]" id="<%= slug %>_class" class="form-control" value="<%= klass %>">
				</div>

				{{-- Target --}}
				<div class="form-group">
					<label class="control-label" for="<%= slug %>_target">{{{ trans('platform/menus::model.target') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.target_help') }}}"></i>

					<div class="controls">
						<select data-item-form="<%= slug %>" name="children[<%= slug %>][target]" id="<%= slug %>_target" class="form-control">
							<option value="self"<%= target == 'self' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::model.targets.self') }}}</option>
							<option value="new_children"<%= target == 'new_children' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::model.targets.blank') }}}</option>
							<option value="parent_frame"<%= target == 'parent_frame' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::model.targets.parent') }}}</option>
							<option value="top_frame"<%= target == 'top_frame' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::model.targets.top') }}}</option>
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
					<label class="control-label" for="<%= slug %>_regex">{{{ trans('platform/menus::model.regex') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.regex_help') }}}"></i>

					<input data-item-form="<%= slug %>" type="text" name="children[<%= slug %>][regex]" id="<%= slug %>_regex" class="form-control" value="<%= regex %>">
				</div>

			</fieldset>

		</div>

	</div>

</div>

</script>
