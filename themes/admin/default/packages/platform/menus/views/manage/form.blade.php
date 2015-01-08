<?php
	$childId   = ! empty($child) ? "{$child->id}_%s" : 'new-child_%s';
	$childName = ! empty($child) ? "children[{$child->id}][%s]" : 'new-child_%s';
	$mode = ! empty($child) ? 'update' : 'create';
	$parentId = ( ! empty($child) and $child->depth > 1) ? $child->getParent()->id : 0;
	$selectedRoles = ! empty($child) ? $child->roles ?: array() : array();
?>

<div class="hide" data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" data-item-parent="{{{ $parentId }}}">

	<input type="hidden" id="{{ sprintf($childId, 'current-slug') }}" value="{{ ! empty($child) ? $child->slug : null }}">

	<h4>
		{{{ trans("platform/menus::model.{$mode}.legend") }}}

		<span class="pull-right"><small class="item-box-close" data-item-close="{{{ ! empty($child) ? $child->id : 'new-child' }}}">&times;</small></span>
	</h4>

	<p>{{{ trans("platform/menus::model.{$mode}.description") }}}</p>

	{{-- Item Details --}}
	<div class="well well-md item-box-borderless">

		<fieldset>

			<legend>Item details</legend>

			{{-- Name --}}
			<div class="form-group">
				<label class="control-label" for="{{ sprintf($childId, 'name') }}">{{{ trans('platform/menus::model.name') }}}</label>

				<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.name_help') }}}"></i>

				<input data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" type="text" name="{{ sprintf($childName, 'name') }}" id="{{ sprintf($childId, 'name') }}" class="form-control" value="{{ ! empty($child) ? $child->name : null }}">
			</div>

			{{-- Slug --}}
			<div class="form-group">
				<label class="control-label" for="{{ sprintf($childId, 'slug') }}">{{{ trans('platform/menus::model.slug') }}}</label>

				<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.slug_help') }}}"></i>

				<input data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" type="text" name="{{ sprintf($childName, 'slug') }}" id="{{ sprintf($childId, 'slug') }}" class="form-control" value="{{ ! empty($child) ? $child->slug : null }}">
			</div>

			{{-- Enabled --}}
			<div class="form-group">
				<label class="control-label" for="{{ sprintf($childId, 'enabled') }}">{{{ trans('platform/menus::model.enabled') }}}</label>

				<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.enabled_help') }}}"></i>

				<div class="controls">
					<select data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, 'enabled') }}" id="{{ sprintf($childId, 'enabled') }}" class="form-control">
						<option value="1"{{ ( ! empty($child) ? $child->enabled : 1) == 1 ? ' selected="selected"' : null }}>{{{ trans('common.enabled') }}}</option>
						<option value="0"{{ ( ! empty($child) ? $child->enabled : 1) == 0 ? ' selected="selected"' : null }}>{{{ trans('common.disabled') }}}</option>
					</select>
				</div>
			</div>

			{{-- Parent --}}
			<div class="form-group">
				<label class="control-label" for="{{ sprintf($childId, 'parent') }}">{{{ trans('platform/menus::model.parent') }}}</label>

				<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.parent_help') }}}"></i>

				<div class="controls">
					<select data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" data-parents name="{{ sprintf($childName, 'parent') }}" id="{{ sprintf($childId, 'parent') }}" class="form-control"></select>
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
						<label class="control-label" for="{{ sprintf($childId, 'type') }}">{{{ trans('platform/menus::model.type') }}}</label>

						<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.type_help') }}}"></i>

						<div class="controls">
							<select data-item-url-type="{{{ ! empty($child) ? $child->id : 'new-child' }}}" data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, 'type') }}" id="{{ sprintf($childId, 'type') }}" class="form-control">
								@foreach ($types as $type)
								<option value="{{ $type->getIdentifier() }}"{{ ( ! empty($child) ? $child->type : null) == $type->getIdentifier() ? ' selected="selected"' : null }}>{{ $type->getName() }}</option>
								@endforeach
							</select>
						</div>
					</div>

				</div>

				<div class="col-md-6">

					{{-- Secure --}}
					<div class="form-group">
						<label class="control-label" for="{{ sprintf($childId, 'secure') }}">{{{ trans('platform/menus::model.secure') }}}</label>

						<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.secure_help') }}}"></i>

						<div class="controls">
							<select data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, 'secure') }}" id="{{ sprintf($childId, 'secure') }}" class="form-control">
								<option value=""{{ ( ! empty($child) ? $child->secure : null) === null ? ' selected="selected"' : null }}>{{{ trans('common.inherit') }}}</option>
								<option value="1"{{ ( ! empty($child) ? $child->secure : null) === true ? ' selected="selected"' : null }}>{{{ trans('common.yes') }}}</option>
								<option value="0"{{ ( ! empty($child) ? $child->secure : null) === false ? ' selected="selected"' : null }}>{{{ trans('common.no') }}}</option>
							</select>
						</div>
					</div>

				</div>

			</div>

			{{-- Generate the types inputs --}}
			@foreach ($types as $type)
				{{ $type->getFormHtml( ! empty($child) ? $child : null) }}
			@endforeach

		</fieldset>

	</div>

	<button type="button" class="btn btn-sm btn-info" data-toggle-options="{{{ ! empty($child) ? $child->id : 'new-child' }}}">{{{ trans('platform/menus::action.more_options') }}}</button>

	<span class="pull-right">
	@if ( ! empty($child))
	<button class="btn btn-sm btn-success" data-item-update="{{{ $child->id }}}">{{{ trans('action.update') }}}</button>
	<button class="btn btn-sm btn-danger" data-item-remove="{{{ $child->id }}}">{{{ trans('action.remove') }}}</button>
	@else
	<button class="btn btn-sm btn-success" data-item-create>{{{ trans('action.add') }}}</button>
	@endif
	</span>

	{{-- Options --}}
	<div class="hide" style="padding-top: 20px;" data-options>

		<div class="well well-md item-box-borderless">

			<fieldset>

				<legend>{{{ trans('platform/menus::model.visibility') }}}</legend>

				{{-- Visibility --}}
				<div class="form-group">
					<label class="control-label" for="{{ sprintf($childId, 'visibility') }}">{{{ trans('platform/menus::model.visibility') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.visibility_help') }}}"></i>

					<div class="controls">
						<select data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" data-item-visibility="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, 'visibility') }}" id="{{ sprintf($childId, 'visibility') }}" class="form-control">
							<option value="always"{{ ( ! empty($child) ? $child->visibility : null) == 'always' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::model.visibilities.always') }}}</option>
							<option value="logged_in"{{ ( ! empty($child) ? $child->visibility : null) == 'logged_in' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::model.visibilities.logged_in') }}}</option>
							<option value="logged_out"{{ ( ! empty($child) ? $child->visibility : null) == 'logged_out' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::model.visibilities.logged_out') }}}</option>
							<option value="admin"{{ ( ! empty($child) ? $child->visibility : null) == 'admin' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::model.visibilities.admin') }}}</option>
						</select>
					</div>
				</div>

				{{-- Roles --}}
				<div class="form-group{{ ! in_array( ! empty($child) ? $child->visibility : null, array('logged_in', 'admin')) ? ' hide' : null }}" data-item-roles="{{{ ! empty($child) ? $child->id : 'new-child' }}}">
					<label class="control-label" for="{{ sprintf($childId, 'roles') }}">{{{ trans('platform/menus::model.roles') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.roles_help') }}}"></i>

					<div class="controls">
						<select data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, 'roles') }}[]" id="{{ sprintf($childId, 'roles') }}" class="form-control" multiple="true">
							@foreach ($roles as $role)
							<option value="{{{ $role->id }}}"{{ in_array($role->id, $selectedRoles) ? ' selected="selected"' : null }}>{{{ $role->name }}}</option>
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
					<label class="control-label" for="{{ sprintf($childId, 'class') }}">{{{ trans('platform/menus::model.class') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.class_help') }}}"></i>

					<input data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" type="text" name="{{ sprintf($childName, 'class') }}" id="{{ sprintf($childId, 'class') }}" class="form-control" value="{{ ! empty($child) ? $child->class : null }}">
				</div>

				{{-- Target --}}
				<div class="form-group">
					<label class="control-label" for="{{ sprintf($childId, 'target') }}">{{{ trans('platform/menus::model.target') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.target_help') }}}"></i>

					<div class="controls">
						<select data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, 'target') }}" id="{{ sprintf($childId, 'target') }}" class="form-control">
							<option value="self"{{ ( ! empty($child) ? $child->target : null) == 'self' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::model.targets.self') }}}</option>
							<option value="new_children"{{ ( ! empty($child) ? $child->target : null) == 'new_children' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::model.targets.blank') }}}</option>
							<option value="parent_frame"{{ ( ! empty($child) ? $child->target : null) == 'parent_frame' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::model.targets.parent') }}}</option>
							<option value="top_frame"{{ ( ! empty($child) ? $child->target : null) == 'top_frame' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::model.targets.top') }}}</option>
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
					<label class="control-label" for="{{ sprintf($childId, 'regex') }}">{{{ trans('platform/menus::model.regex') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.regex_help') }}}"></i>

					<input data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" type="text" name="{{ sprintf($childName, 'regex') }}" id="{{ sprintf($childId, 'regex') }}" class="form-control" value="{{ ! empty($child) ? $child->regex : null }}">
				</div>

			</fieldset>

		</div>

	</div>

</div>

@if ( ! empty($child) and $children = $child->getChildren())
@each('platform/menus::manage/form', $children, 'child')
@endif
