<div data-template class="well well-md hide" data-item-form="[[ slug ]]">

	<input type="hidden" name="children[[[ slug ]]][id]" value="[[ slug ]]">

	<fieldset>

		<legend>
			[[ name ]]{{ trans('platform/menus::form.child.edit.legend') }}

			<span class="pull-right"><small class="item-close" data-item="[[ slug ]]" data-item-close>&times;</small></span>
		</legend>

		{{-- Item Name --}}
		<div class="form-group">
			<label class="control-label" for="[[ slug ]]_name">{{ trans('platform/menus::form.child.name') }}</label>
			<input type="text" data-children="[[ slug ]]" name="children[[[ slug ]]][name]" id="[[ slug ]]_name" class="form-control" value="[[ name ]]">
		</div>

		{{-- Item Slug --}}
		<div class="form-group">
			<label class="control-label" for="[[ slug ]]_slug">{{ trans('platform/menus::form.child.slug') }}</label>
			<input type="text" data-children="[[ slug ]]" name="children[[[ slug ]]][slug]" id="[[ slug ]]_slug" class="form-control" value="[[ slug ]]">
		</div>

		{{-- Item Type --}}
		<div class="form-group">
			<label class="control-label" for="[[ slug ]]_type">{{ trans('platform/menus::form.child.type.title') }}</label>
			<div class="controls">
				<select data-children="[[ slug ]]" name="children[[[ slug ]]][type]" id="[[ slug ]]_type" class="form-control">
					[? if type == 'static' ?]
					<option value="static"  selected="selected">{{ trans('platform/menus::form.child.type.static') }}</option>
					[? else ?]
					<option value="static">{{ trans('platform/menus::form.child.type.static') }}</option>
					[? endif ?]

					[? if type == 'page' ?]
					<option value="page" selected="selected">{{ trans('platform/menus::form.child.type.page') }}</option>
					[? else ?]
					<option value="page">{{ trans('platform/menus::form.child.type.page') }}</option>
					[? endif ?]
				</select>
			</div>
		</div>

		{{-- Item Uri --}}
		<div class="form-group">
			<label class="control-label" for="[[ slug ]]_uri">{{ trans('platform/menus::form.child.uri') }}</label>
			<input type="text" data-children="[[ slug ]]" name="children[[[ slug ]]][uri]" id="[[ slug ]]_uri" class="form-control" value="[[ uri ]]">
		</div>

		{{-- Visibility --}}
		<div class="form-group">
			<label class="control-label" for="[[ slug ]]_visibility">{{ trans('platform/menus::form.child.visibility.title') }}</label>
			<div class="controls">
				<select data-children="[[ slug ]]" name="children[[[ slug ]]][visibility]" id="[[ slug ]]_visibility" class="form-control">
					[? if visibility == 'always' ?]
					<option value="always" selected="selected">{{ trans('platform/menus::form.child.visibility.always') }}</option>
					[? else ?]
					<option value="always">{{ trans('platform/menus::form.child.visibility.always') }}</option>
					[? endif ?]

					[? if visibility == 'logged_in' ?]
					<option value="logged_in" selected="selected">{{ trans('platform/menus::form.child.visibility.logged_in') }}</option>
					[? else ?]
					<option value="logged_in">{{ trans('platform/menus::form.child.visibility.logged_in') }}</option>
					[? endif ?]

					[? if visibility == 'logged_out' ?]
					<option value="logged_out" selected="selected">{{ trans('platform/menus::form.child.visibility.logged_out') }}</option>
					[? else ?]
					<option value="logged_out">{{ trans('platform/menus::form.child.visibility.logged_out') }}</option>
					[? endif ?]

					[? if visibility == 'admin' ?]
					<option value="admin" selected="selected">{{ trans('platform/menus::form.child.visibility.admin') }}</option>
					[? else ?]
					<option value="admin">{{ trans('platform/menus::form.child.visibility.admin') }}</option>
					[? endif ?]
				</select>
			</div>
		</div>

		{{-- Secure --}}
		<div class="form-group">
			<label class="control-label" for="[[ slug ]]_secure">{{ trans('platform/menus::form.child.secure') }}</label>
			<div class="controls">
				<select data-children="[[ slug ]]" name="children[[[ slug ]]][secure]" id="[[ slug ]]_secure" class="form-control">
					[? if secure == '1' ?]
					<option value="1" selected="selected">{{ trans('general.yes') }}</option>
					[? else ?]
					<option value="1">{{ trans('general.yes') }}</option>
					[? endif ?]

					[? if secure == '0' ?]
					<option value="0" selected="selected">{{ trans('general.no') }}</option>
					[? else ?]
					<option value="0">{{ trans('general.no') }}</option>
					[? endif ?]
				</select>
			</div>
		</div>

		{{-- Target --}}
		<div class="form-group">
			<label class="control-label" for="[[ slug ]]_target">{{ trans('platform/menus::form.child.target.title') }}</label>
			<div class="controls">
				<select data-children="[[ slug ]]" name="children[[[ slug ]]][target]" id="[[ slug ]]_target" class="form-control">
					[? if target == 'self' ?]
					<option value="self" selected="selected">{{ trans('platform/menus::form.child.target.self') }}</option>
					[? else ?]
					<option value="self">{{ trans('platform/menus::form.child.target.self') }}</option>
					[? endif ?]

					[? if target == 'new_children' ?]
					<option value="new_children" selected="selected">{{ trans('platform/menus::form.child.target.blank') }}</option>
					[? else ?]
					<option value="new_children">{{ trans('platform/menus::form.child.target.blank') }}</option>
					[? endif ?]

					[? if target == 'parent_frame' ?]
					<option value="parent_frame" selected="selected">{{ trans('platform/menus::form.child.target.parent') }}</option>
					[? else ?]
					<option value="parent_frame">{{ trans('platform/menus::form.child.target.parent') }}</option>
					[? endif ?]

					[? if target == 'top_frame' ?]
					<option value="top_frame" selected="selected">{{ trans('platform/menus::form.child.target.top') }}</option>
					[? else ?]
					<option value="top_frame">{{ trans('platform/menus::form.child.target.top') }}</option>
					[? endif ?]
				</select>
			</div>
		</div>

		{{-- CSS Class --}}
		<div class="form-group">
			<label class="control-label" for="[[ slug ]]_class">{{ trans('platform/menus::form.child.class') }}</label>
			<input type="text" data-children="[[ slug ]]" name="children[[[ slug ]]][class]" id="[[ slug ]]_class" class="form-control" value="[[ class ]]">
		</div>

		{{-- Enabled --}}
		<div class="form-group">
			<label class="control-label" for="[[ slug ]]_enabled">{{ trans('platform/menus::form.child.enabled') }}</label>
			<div class="controls">
				<select data-children="[[ slug ]]" name="children[[[ slug ]]][enabled]" id="[[ slug ]]_enabled" class="form-control">
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

		<button class="btn btn-sm btn-success" data-item-update>{{ trans('button.update') }}</button>

		<button class="btn btn-sm btn-danger" data-item-remove data-item-form="[[ slug ]]">{{ trans('button.remove') }}</button>

	</fieldset>

</div>
