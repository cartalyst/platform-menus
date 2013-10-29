<div data-template class="well well-md hide" style="background: #fff" data-item-form="[[ slug ]]" data-item-parent="[[ parent_id ]]">

	<input type="hidden" id="[[ slug ]]_current-slug" value="[[ slug ]]">

	<h4>
		{{{ trans('platform/menus::form.edit.legend') }}}

		<span class="pull-right"><small class="item-close" data-item-close="[[ slug ]]">&times;</small></span>
	</h4>

	<p>{{{ trans('platform/menus::form.edit.description') }}}</p>

	{{-- Item details --}}
	<div class="well well-md" style="border: none; border-radius: none; box-shadow: none;">

		<fieldset>

			<legend>Item details</legend>

			{{-- Name --}}
			<div class="form-group">
				<label class="control-label" for="[[ slug ]]_name">{{{ trans('platform/menus::form.name') }}}</label>

				<i class="icon-info-sign" data-toggle="popover" data-content="{{{ trans('platform/menus::form.name_help') }}}"></i>

				<input data-item-form="[[ slug ]]" type="text" name="children[[[ slug ]]][name]" id="[[ slug ]]_name" class="form-control" value="[[ name ]]">
			</div>

			{{-- Slug --}}
			<div class="form-group">
				<label class="control-label" for="[[ slug ]]_slug">{{{ trans('platform/menus::form.slug') }}}</label>

				<i class="icon-info-sign" data-toggle="popover" data-content="{{{ trans('platform/menus::form.slug_help') }}}"></i>

				<input data-item-form="[[ slug ]]" type="text" name="children[[[ slug ]]][slug]" id="[[ slug ]]_slug" class="form-control" value="[[ slug ]]">
			</div>

			{{-- Enabled --}}
			<div class="form-group">
				<label class="control-label" for="[[ slug ]]_enabled">{{ trans('platform/menus::form.enabled') }}</label>
				<div class="controls">
					<select data-item-form="[[ slug ]]" name="children[[[ slug ]]][enabled]" id="[[ slug ]]_enabled" class="form-control">
						[? if enabled == '1' ?]
						<option value="1" selected="selected">{{ trans('general.enabled') }}</option>
						[? else ?]
						<option value="1">{{ trans('general.enabled') }}</option>
						[? endif ?]

						[? if enabled == '0' ?]
						<option value="0" selected="selected">{{ trans('general.disabled') }}</option>
						[? else ?]
						<option value="0">{{ trans('general.disabled') }}</option>
						[? endif ?]
					</select>
				</div>
			</div>

			{{-- Parent --}}
			<div class="form-group">
				<label class="control-label" for="[[ slug ]]_parent">{{{ trans('platform/menus::form.parent') }}}</label>

				<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.parent_help') }}}"></i>

				<div class="controls">
					<select data-item-form="[[ slug ]]" data-parents id="[[ slug ]]_parent" class="form-control"></select>
				</div>
			</div>

		</fieldset>

	</div>

	{{-- Item URL --}}
	<div class="well well-md" style="border: none; border-radius: none; box-shadow: none;">

		<fieldset>

			<legend>Item URL</legend>

			<div class="row">

				<div class="col-md-6">

					{{-- Item Type --}}
					<div class="form-group">
						<label class="control-label" for="[[ slug ]]_type">{{{ trans('platform/menus::form.type') }}}</label>

						<i class="icon-info-sign" data-toggle="popover" data-content="{{{ trans('platform/menus::form.type_help') }}}"></i>

						<div class="controls">
							<select data-item-url-type="[[ slug ]]" data-item-form="[[ slug ]]" name="children[[[ slug ]]][type]" id="[[ slug ]]_type" class="form-control">
								@foreach ($types as $type)
								<option value="{{ $type->getIdentifier() }}">{{ $type->getName() }}</option>
								@endforeach
							</select>
						</div>
					</div>

				</div>

				<div class="col-md-6">

					{{-- Secure --}}
					<div class="form-group">
						<label class="control-label" for="[[ slug ]]_secure">{{{ trans('platform/menus::form.secure') }}}</label>
						<div class="controls">
							<select data-item-form="[[ slug ]]" name="children[[[ slug ]]][secure]" id="[[ slug ]]_secure" class="form-control">
								[? if secure == '1' ?]
								<option value="1" selected="selected">{{{ trans('general.yes') }}}</option>
								[? else ?]
								<option value="1">{{{ trans('general.yes') }}}</option>
								[? endif ?]

								[? if secure == '0' ?]
								<option value="0" selected="selected">{{{ trans('general.no') }}}</option>
								[? else ?]
								<option value="0">{{{ trans('general.no') }}}</option>
								[? endif ?]
							</select>
						</div>
					</div>

				</div>

			</div>

			{{-- Generate the types inputs --}}
			@foreach ($types as $type)
				{{ $type->getTemplateHtml($child) }}
			@endforeach

		</fieldset>

	</div>

	<button type="button" class="btn btn-sm btn-info" data-toggle-options="[[ slug ]]">More options</button>

	<span class="pull-right">
		<button class="btn btn-sm btn-success" data-item-update="[[ slug ]]">{{{ trans('button.update') }}}</button>

		<button class="btn btn-sm btn-danger" data-item-remove="[[ slug ]]">{{{ trans('button.remove') }}}</button>
	</span>

	{{-- Options --}}
	<div class="hide" style="padding-top: 20px;" data-options>

		<div class="well well-md" style="border: none; border-radius: none; box-shadow: none;">

			<fieldset>

				<legend>{{{ trans('platform/menus::form.visibility') }}}</legend>

				{{-- Visibility --}}
				<div class="form-group">
					<label class="control-label" for="[[ slug ]]_visibility">{{ trans('platform/menus::form.visibility') }}</label>

					<i class="icon-info-sign" data-toggle="popover" data-content="{{{ trans('platform/menus::form.visibility_help') }}}"></i>

					<div class="controls">
						<select data-item-form="[[ slug ]]" data-item-visibility="[[ slug ]]" name="children[[[ slug ]]][visibility]" id="[[ slug ]]_visibility" class="form-control">
							[? if visibility == 'always' ?]
							<option value="always" selected="selected">{{ trans('platform/menus::form.visibilities.always') }}</option>
							[? else ?]
							<option value="always">{{ trans('platform/menus::form.visibilities.always') }}</option>
							[? endif ?]

							[? if visibility == 'logged_in' ?]
							<option value="logged_in" selected="selected">{{ trans('platform/menus::form.visibilities.logged_in') }}</option>
							[? else ?]
							<option value="logged_in">{{ trans('platform/menus::form.visibilities.logged_in') }}</option>
							[? endif ?]

							[? if visibility == 'logged_out' ?]
							<option value="logged_out" selected="selected">{{ trans('platform/menus::form.visibilities.logged_out') }}</option>
							[? else ?]
							<option value="logged_out">{{ trans('platform/menus::form.visibilities.logged_out') }}</option>
							[? endif ?]

							[? if visibility == 'admin' ?]
							<option value="admin" selected="selected">{{ trans('platform/menus::form.visibilities.admin') }}</option>
							[? else ?]
							<option value="admin">{{ trans('platform/menus::form.visibilities.admin') }}</option>
							[? endif ?]
						</select>
					</div>
				</div>

				{{-- Groups --}}
				<!--
					@todo - Once the item is added i need to check if i show the groups div or not..
				-->
				<div class="form-group hide" data-item-groups="[[ slug ]]">
					<label class="control-label" for="[[ slug ]]_groups">{{{ trans('platform/menus::form.groups') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.groups_help') }}}"></i>

					<div class="controls">
						<select data-item-form="[[ slug ]]" name="children[[[ slug ]]][groups]" id="[[ slug ]]_groups" class="form-control" multiple="true">
							@foreach ($groups as $id => $name)
							<option value="{{{ $id }}}">{{{ $name }}}</option>
							@endforeach
						</select>
					</div>
				</div>

			</fieldset>

		</div>

		{{-- Attributes --}}
		<div class="well well-md" style="border: none; border-radius: none; box-shadow: none;">

			<fieldset>

				<legend>Attributes</legend>

				{{-- ID --}}
				<div class="form-group">
					<label class="control-label" for="[[ slug ]]_attribute_id">{{{ trans('platform/menus::form.attributes.id') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.attributes.id_help') }}}"></i>

					<input data-item-form="[[ slug ]]" type="text" name="children[[[ slug ]]][attribute_id]" id="[[ slug ]]_attribute_id" class="form-control" value="[[ attribute_id ]]">
				</div>

				{{-- Class --}}
				<div class="form-group">
					<label class="control-label" for="[[ slug ]]_attribute_class">{{{ trans('platform/menus::form.attributes.class') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.attributes.class_help') }}}"></i>

					<input data-item-form="[[ slug ]]" type="text" name="children[[[ slug ]]][attribute_class]" id="[[ slug ]]_attribute_class" class="form-control" value="[[ attribute_class ]]">
				</div>

				{{-- Name --}}
				<div class="form-group">
					<label class="control-label" for="[[ slug ]]_attribute_name">{{{ trans('platform/menus::form.attributes.name') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.attributes.name_help') }}}"></i>

					<input data-item-form="[[ slug ]]" type="text" name="children[[[ slug ]]][attribute_name]" id="[[ slug ]]_attribute_name" class="form-control" value="[[ attribute_name ]]">
				</div>

				{{-- Title --}}
				<div class="form-group">
					<label class="control-label" for="[[ slug ]]_attribute_title">{{{ trans('platform/menus::form.attributes.title') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.attributes.title_help') }}}"></i>

					<input data-item-form="[[ slug ]]" type="text" name="children[[[ slug ]]][attribute_title]" id="[[ slug ]]_attribute_title" class="form-control" value="[[ attribute_title ]]">
				</div>

				{{-- Target --}}
				<div class="form-group">
					<label class="control-label" for="[[ slug ]]_attribute_target">{{{ trans('platform/menus::form.attributes.target') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.attributes.target_help') }}}"></i>

					<div class="controls">
						<select data-item-form="[[ slug ]]" name="children[[[ slug ]]][attribute_target]" id="[[ slug ]]_attribute_target" class="form-control">
							[? if attribute_target == 'self' ?]
							<option value="self" selected="selected">{{{ trans('platform/menus::form.attributes.targets.self') }}}</option>
							[? else ?]
							<option value="self">{{{ trans('platform/menus::form.attributes.targets.self') }}}</option>
							[? endif ?]

							[? if attribute_target == 'new_children' ?]
							<option value="new_children" selected="selected">{{{ trans('platform/menus::form.attributes.targets.blank') }}}</option>
							[? else ?]
							<option value="new_children">{{{ trans('platform/menus::form.attributes.targets.blank') }}}</option>
							[? endif ?]

							[? if attribute_target == 'parent_frame' ?]
							<option value="parent_frame" selected="selected">{{{ trans('platform/menus::form.attributes.targets.parent') }}}</option>
							[? else ?]
							<option value="parent_frame">{{{ trans('platform/menus::form.attributes.targets.parent') }}}</option>
							[? endif ?]

							[? if attribute_target == 'top_frame' ?]
							<option value="top_frame" selected="selected">{{{ trans('platform/menus::form.attributes.targets.top') }}}</option>
							[? else ?]
							<option value="top_frame">{{{ trans('platform/menus::form.attributes.targets.top') }}}</option>
							[? endif ?]
						</select>
					</div>
				</div>

			</fieldset>

		</div>

	</div>

</div>
