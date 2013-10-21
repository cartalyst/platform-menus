<?php
	$childId   = ! empty($child) ? $child->id : 'new-child';
	$childName = ! empty($child) ? "children[{$child->id}][%s]" : 'new-child_%s';
	$dataChild = ! empty($child) ? ' data-children="' . $child->id . '"' : null;
	$segment = ! empty($child) ? 'edit' : 'create';
?>
<div class="well well-md hide" data-item-form="{{ $childId }}">

	<fieldset>

		<legend>
			{{ ! empty($child) ? $child->name : null }}{{ trans("platform/menus::form.child.{$segment}.legend") }}

			<span class="pull-right"><small class="item-close" data-item="{{ $childId }}" data-item-close>&times;</small></span>
		</legend>

		{{-- Item Name --}}
		<div class="form-group">
			<label class="control-label" for="{{ $childId }}_name">{{ trans('platform/menus::form.child.name') }}</label>
			<input type="text"{{ $dataChild }} name="{{ sprintf($childName, 'name') }}" id="{{ $childId }}_name" class="form-control" value="{{ ! empty($child) ? $child->name : null }}">
		</div>

		{{-- Item Slug --}}
		<div class="form-group">
			<label class="control-label" for="{{ $childId }}_slug">{{ trans('platform/menus::form.child.slug') }}</label>
			<input type="text"{{ $dataChild }} name="{{ sprintf($childName, 'slug') }}" id="{{ $childId }}_slug" class="form-control" value="{{ ! empty($child) ? $child->slug : null }}">
		</div>

		{{-- Item Type --}}
		<div class="form-group">
			<label class="control-label" for="{{ $childId }}_type">{{ trans('platform/menus::form.child.type.title') }}</label>
			<div class="controls">
				<select{{ $dataChild }} name="{{ sprintf($childName, 'type') }}" id="{{ $childId }}_type" class="form-control">
					<option value="static"{{ ( ! empty($child) ? $child->type : null) == 'static' ? ' selected="selected"' : null }}>{{ trans('platform/menus::form.child.type.static') }}</option>
					<option value="page"{{ ( ! empty($child) ? $child->type : null) == 'page' ? ' selected="selected"' : null }}>{{ trans('platform/menus::form.child.type.page') }}</option>
				</select>
			</div>
		</div>

		{{-- Item Uri --}}
		<div class="form-group">
			<label class="control-label" for="{{ $childId }}_uri">{{ trans('platform/menus::form.child.uri') }}</label>
			<input type="text"{{ $dataChild }} name="{{ sprintf($childName, 'uri') }}" id="{{ $childId }}_uri" class="form-control" value="{{ ! empty($child) ? $child->uri : null }}">
		</div>

		{{-- Visibility --}}
		<div class="form-group">
			<label class="control-label" for="{{ $childId }}_visibility">{{ trans('platform/menus::form.child.visibility.title') }}</label>
			<div class="controls">
				<select{{ $dataChild }} name="{{ sprintf($childName, 'visibility') }}" id="{{ $childId }}_visibility" class="form-control">
					<option value="always"{{ ( ! empty($child) ? $child->visibility : null) == 'always' ? ' selected="selected"' : null }}>{{ trans('platform/menus::form.child.visibility.always') }}</option>
					<option value="logged_in"{{ ( ! empty($child) ? $child->visibility : null) == 'logged_in' ? ' selected="selected"' : null }}>{{ trans('platform/menus::form.child.visibility.logged_in') }}</option>
					<option value="logged_out"{{ ( ! empty($child) ? $child->visibility : null) == 'logged_out' ? ' selected="selected"' : null }}>{{ trans('platform/menus::form.child.visibility.logged_out') }}</option>
					<option value="admin"{{ ( ! empty($child) ? $child->visibility : null) == 'admin' ? ' selected="selected"' : null }}>{{ trans('platform/menus::form.child.visibility.admin') }}</option>
				</select>
			</div>
		</div>

		{{-- Secure --}}
		<div class="form-group">
			<label class="control-label" for="{{ $childId }}_secure">{{ trans('platform/menus::form.child.secure') }}</label>
			<div class="controls">
				<select{{ $dataChild }} name="{{ sprintf($childName, 'secure') }}" id="{{ $childId }}_secure" class="form-control">
					<option value="1"{{ ( ! empty($child) ? $child->secure : null) == 1 ? ' selected="selected"' : null }}>{{ trans('general.yes') }}</option>
					<option value="0"{{ ( ! empty($child) ? $child->secure : null) == 0 ? ' selected="selected"' : null }}>{{ trans('general.no') }}</option>
				</select>
			</div>
		</div>

		{{-- Target --}}
		<div class="form-group">
			<label class="control-label" for="{{ $childId }}_target">{{ trans('platform/menus::form.child.target.title') }}</label>
			<div class="controls">
				<select{{ $dataChild }} name="{{ sprintf($childName, 'target') }}" id="{{ $childId }}_target" class="form-control">
					<option value="self"{{ ( ! empty($child) ? $child->target : null) == 'self' ? ' selected="selected"' : null }}>{{ trans('platform/menus::form.child.target.self') }}</option>
					<option value="new_children"{{ ( ! empty($child) ? $child->target : null) == 'new_children' ? ' selected="selected"' : null }}>{{ trans('platform/menus::form.child.target.blank') }}</option>
					<option value="parent_frame"{{ ( ! empty($child) ? $child->target : null) == 'parent_frame' ? ' selected="selected"' : null }}>{{ trans('platform/menus::form.child.target.parent') }}</option>
					<option value="top_frame"{{ ( ! empty($child) ? $child->target : null) == 'top_frame' ? ' selected="selected"' : null }}>{{ trans('platform/menus::form.child.target.top') }}</option>
				</select>
			</div>
		</div>

		{{-- CSS Class --}}
		<div class="form-group">
			<label class="control-label" for="{{ $childId }}_class">{{ trans('platform/menus::form.child.class') }}</label>
			<input type="text"{{ $dataChild }} name="{{ sprintf($childName, 'class') }}" id="{{ $childId }}_class" class="form-control" value="{{ ! empty($child) ? $child->class : null }}">
		</div>

		{{-- Enabled --}}
		<div class="form-group">
			<label class="control-label" for="{{ $childId }}_enabled">{{ trans('platform/menus::form.child.enabled') }}</label>
			<div class="controls">
				<select{{ $dataChild }} name="{{ sprintf($childName, 'enabled') }}" id="{{ $childId }}_enabled" class="form-control">
					<option value="1"{{ ( ! empty($child) ? $child->enabled : 1) == 1 ? ' selected="selected"' : null }}>{{ trans('general.enabled') }}</option>
					<option value="0"{{ ( ! empty($child) ? $child->enabled : 1) == 0 ? ' selected="selected"' : null }}>{{ trans('general.disabled') }}</option>
				</select>
			</div>
		</div>

	</fieldset>

	@if ( ! empty($child))
	<button class="btn btn-sm btn-success" data-item-update>{{{ trans('button.update') }}}</button>
	<button class="btn btn-sm btn-danger" data-item-remove data-item-form="{{{ $child->id }}}">{{{ trans('button.remove') }}}</button>
	@else
	<button class="btn btn-sm btn-success" data-item-create>{{{ trans('button.create') }}}</button>
	@endif

</div>

@if ( ! empty($child) and $children = $child->getChildren())
@each('platform/menus::form', $children, 'child')
@endif
