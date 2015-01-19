<?php
$childId   = ! empty($child) ? "{$child->id}_%s" : 'new-child_%s';
$childName = ! empty($child) ? "children[{$child->id}][%s]" : 'new-child_%s';
$mode = ! empty($child) ? 'update' : 'create';
$parentId = ( ! empty($child) and $child->depth > 1) ? $child->getParent()->id : 0;
$selectedRoles = ! empty($child) ? $child->roles ?: array() : array();
?>

<div class="item-form" data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" data-item-parent="{{{ $parentId }}}">

	<input type="hidden" id="{{ sprintf($childId, 'current-slug') }}" value="{{ ! empty($child) ? $child->slug : null }}">

	{{-- Item Details --}}
	<fieldset>

		<legend>

			{{{ trans('platform/menus::model.general.item_details') }}}

			@if ( ! empty($child))
			<span class="pull-right" data-item-update="{{{ $child->id }}}"><i class="fa fa-save"></i> {{{ trans('action.update') }}}</span>
			<span class="pull-right" data-item-remove="{{{ $child->id }}}"><i class="fa fa-trash"></i> {{{ trans('action.remove') }}}</span>
			@else
			<span class="pull-right" data-item-create><i class="fa fa-plus"></i> {{{ trans('action.add') }}}</span>
			@endif

			<span class="pull-right" data-toggle-options="{{{ ! empty($child) ? $child->id : 'new-child' }}}"><i class="fa fa-wrench"></i> {{{ trans('platform/menus::model.general.advanced_settings') }}}</span>

		</legend>

		<div class="row">

			<div class="col-sm-3">

				{{-- Name --}}
				<div class="form-group">

					<label class="control-label" for="{{ sprintf($childId, 'name') }}">
						<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.name_item_help') }}}"></i>
						{{{ trans('platform/menus::model.general.name_item') }}}
					</label>

					<input class="form-control input-sm" data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" type="text" name="{{ sprintf($childName, 'name') }}" id="{{ sprintf($childId, 'name') }}" value="{{ ! empty($child) ? $child->name : null }}">

				</div>

			</div>

			<div class="col-sm-3">

				{{-- Slug --}}
				<div class="form-group">
					<label class="control-label" for="{{ sprintf($childId, 'slug') }}">
						<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.slug_item_help') }}}"></i>
						{{{ trans('platform/menus::model.general.slug_item') }}}
					</label>

					<input class="form-control input-sm" data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" type="text" name="{{ sprintf($childName, 'slug') }}" id="{{ sprintf($childId, 'slug') }}" value="{{ ! empty($child) ? $child->slug : null }}">

				</div>

			</div>

			<div class="col-sm-2">

				{{-- Item Type --}}
				<div class="form-group">
					<label class="control-label" for="{{ sprintf($childId, 'type') }}">
						<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.type_help') }}}"></i>
						{{{ trans('platform/menus::model.general.type') }}}
					</label>

					<div class="controls">
						<select class="form-control input-sm" data-item-url-type="{{{ ! empty($child) ? $child->id : 'new-child' }}}" data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, 'type') }}" id="{{ sprintf($childId, 'type') }}">
							@foreach ($types as $type)
							<option value="{{ $type->getIdentifier() }}"{{ ( ! empty($child) ? $child->type : null) == $type->getIdentifier() ? ' selected="selected"' : null }}>{{ $type->getName() }}</option>
							@endforeach
						</select>
					</div>
				</div>

			</div>

			<div class="col-sm-4">

				{{-- Generate the types inputs --}}
				@foreach ($types as $type)
				{{ $type->getFormHtml( ! empty($child) ? $child : null) }}
				@endforeach

			</div>

		</div>

	</fieldset>

	{{-- Options --}}
	<div  class="hide" data-options>

		<fieldset>

			<legend>{{{ trans('platform/menus::model.general.advanced_settings') }}}</legend>

			<div class="row">

				<div class="col-sm-3">

					{{-- Enabled --}}
					<div class="form-group">
						<label class="control-label" for="{{ sprintf($childId, 'enabled') }}">
							<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.enabled_help') }}}"></i>
							{{{ trans('platform/menus::model.general.enabled') }}}
						</label>

						<div class="controls">
							<select data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, 'enabled') }}" id="{{ sprintf($childId, 'enabled') }}" class="form-control input-sm">
								<option value="1"{{ ( ! empty($child) ? $child->enabled : 1) == 1 ? ' selected="selected"' : null }}>{{{ trans('common.enabled') }}}</option>
								<option value="0"{{ ( ! empty($child) ? $child->enabled : 1) == 0 ? ' selected="selected"' : null }}>{{{ trans('common.disabled') }}}</option>
							</select>
						</div>
					</div>

				</div>

				<div class="col-sm-3">

					{{-- Target --}}
					<div class="form-group">

						<label class="control-label" for="{{ sprintf($childId, 'target') }}">
							<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.target_help') }}}"></i>
							{{{ trans('platform/menus::model.general.target') }}}
						</label>

						<div class="controls">
							<select data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, 'target') }}" id="{{ sprintf($childId, 'target') }}" class="form-control input-sm">
								<option value="self"{{ ( ! empty($child) ? $child->target : null) == 'self' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::model.general.targets.self') }}}</option>
								<option value="new_children"{{ ( ! empty($child) ? $child->target : null) == 'new_children' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::model.general.targets.blank') }}}</option>
								<option value="parent_frame"{{ ( ! empty($child) ? $child->target : null) == 'parent_frame' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::model.general.targets.parent') }}}</option>
								<option value="top_frame"{{ ( ! empty($child) ? $child->target : null) == 'top_frame' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::model.general.targets.top') }}}</option>
							</select>
						</div>

					</div>

				</div>

				<div class="col-sm-3">

					{{-- Secure --}}
					<div class="form-group">
						<label class="control-label" for="{{ sprintf($childId, 'secure') }}">
							<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.secure_help') }}}"></i>
							{{{ trans('platform/menus::model.general.secure') }}}
						</label>

						<div class="controls">
							<select data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, 'secure') }}" id="{{ sprintf($childId, 'secure') }}" class="form-control input-sm">
								<option value=""{{ ( ! empty($child) ? $child->secure : null) === null ? ' selected="selected"' : null }}>{{{ trans('common.inherit') }}}</option>
								<option value="1"{{ ( ! empty($child) ? $child->secure : null) === true ? ' selected="selected"' : null }}>{{{ trans('common.yes') }}}</option>
								<option value="0"{{ ( ! empty($child) ? $child->secure : null) === false ? ' selected="selected"' : null }}>{{{ trans('common.no') }}}</option>
							</select>
						</div>
					</div>

				</div>

				<div class="col-sm-3">

					{{-- Parent --}}
					<div class="form-group">

						<label class="control-label" for="{{ sprintf($childId, 'parent') }}">
							<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.parent_help') }}}"></i>
							{{{ trans('platform/menus::model.general.parent') }}}
						</label>

						<div class="controls">
							<select class="form-control input-sm" data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" data-parents name="{{ sprintf($childName, 'parent') }}" id="{{ sprintf($childId, 'parent') }}"></select>
						</div>

					</div>

				</div>

			</div>

			<div class="row">

				<div class="col-sm-4">

					{{-- Class --}}
					<div class="form-group">

						<label class="control-label" for="{{ sprintf($childId, 'class') }}">
							<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.class_help') }}}"></i>
							{{{ trans('platform/menus::model.general.class') }}}
						</label>

						<input data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" type="text" name="{{ sprintf($childName, 'class') }}" id="{{ sprintf($childId, 'class') }}" class="form-control input-sm" value="{{ ! empty($child) ? $child->class : null }}">

					</div>

				</div>

				<div class="col-sm-4">

					{{-- Regular Expression --}}
					<div class="form-group">

						<label class="control-label" for="{{ sprintf($childId, 'regex') }}">
							<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.regex_help') }}}"></i>
							{{{ trans('platform/menus::model.general.regex') }}}
						</label>

						<input data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" type="text" name="{{ sprintf($childName, 'regex') }}" id="{{ sprintf($childId, 'regex') }}" class="form-control input-sm" value="{{ ! empty($child) ? $child->regex : null }}">

					</div>

				</div>

				<div class="col-sm-4">

					{{-- Visibility --}}
					<div class="form-group">

						<label class="control-label" for="{{ sprintf($childId, 'visibility') }}">
							<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.visibility_help') }}}"></i>
							{{{ trans('platform/menus::model.general.visibility') }}}
						</label>

						<div class="controls">

							<select data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" data-item-visibility="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, 'visibility') }}" id="{{ sprintf($childId, 'visibility') }}" class="form-control input-sm">
								<option value="always"{{ ( ! empty($child) ? $child->visibility : null) == 'always' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::model.general.visibilities.always') }}}</option>
								<option value="logged_in"{{ ( ! empty($child) ? $child->visibility : null) == 'logged_in' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::model.general.visibilities.logged_in') }}}</option>
								<option value="logged_out"{{ ( ! empty($child) ? $child->visibility : null) == 'logged_out' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::model.general.visibilities.logged_out') }}}</option>
								<option value="admin"{{ ( ! empty($child) ? $child->visibility : null) == 'admin' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::model.general.visibilities.admin') }}}</option>
							</select>
						</div>
					</div>

				</div>

			</div>

			<div class="row">

				<div class="col-sm-12">

					{{-- Roles --}}
					<div class="form-group{{ ! in_array( ! empty($child) ? $child->visibility : null, array('logged_in', 'admin')) ? ' hide' : null }}" data-item-roles="{{{ ! empty($child) ? $child->id : 'new-child' }}}">

						<label class="control-label" for="{{ sprintf($childId, 'roles') }}">
							<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.roles_help') }}}"></i>
							{{{ trans('platform/menus::model.general.roles') }}}
						</label>

						<div class="controls">
							<select data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, 'roles') }}[]" id="{{ sprintf($childId, 'roles') }}" class="form-control input-sm" multiple="true">
								@foreach ($roles as $role)
								<option value="{{{ $role->id }}}"{{ in_array($role->id, $selectedRoles) ? ' selected="selected"' : null }}>{{{ $role->name }}}</option>
								@endforeach
							</select>
						</div>

					</div>

				</div>

			</div>

		</fieldset>

	</div>

</div>
