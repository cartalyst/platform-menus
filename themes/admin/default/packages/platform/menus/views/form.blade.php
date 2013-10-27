<?php
	$childId   = ! empty($child) ? "{$child->id}_%s" : 'new-child_%s';
	$childName = ! empty($child) ? "children[{$child->id}][%s]" : 'new-child_%s';
	$segment = ! empty($child) ? 'edit' : 'create';
	$parentId = ( ! empty($child) and $child->depth > 1) ? $child->getParent()->id : 0;
?>
<div class="well well-md hide" style="background: #fff" data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" data-item-parent="{{{ $parentId }}}">

	<input type="hidden" id="{{ sprintf($childId, 'current-slug') }}" value="{{ ! empty($child) ? $child->slug : null }}">

	<h4>
		{{{ trans("platform/menus::form.{$segment}.legend") }}}

		<span class="pull-right"><small class="item-close" data-item-close="{{{ ! empty($child) ? $child->id : null }}}">&times;</small></span>
	</h4>

	<p>{{{ trans("platform/menus::form.{$segment}.description") }}}</p>

	{{-- Item Details --}}
	<div class="well well-md" style="border: none; border-radius: none; box-shadow: none;">

		<fieldset>

			<legend>Item details</legend>

			{{-- Name --}}
			<div class="form-group">
				<label class="control-label" for="{{ sprintf($childId, 'name') }}">{{{ trans('platform/menus::form.name') }}}</label>

				<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.name_help') }}}"></i>

				<input data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" type="text" name="{{ sprintf($childName, 'name') }}" id="{{ sprintf($childId, 'name') }}" class="form-control" value="{{ ! empty($child) ? $child->name : null }}">
			</div>

			{{-- Slug --}}
			<div class="form-group">
				<label class="control-label" for="{{ sprintf($childId, 'slug') }}">{{{ trans('platform/menus::form.slug') }}}</label>

				<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.slug_help') }}}"></i>

				<input data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" type="text" name="{{ sprintf($childName, 'slug') }}" id="{{ sprintf($childId, 'slug') }}" class="form-control" value="{{ ! empty($child) ? $child->slug : null }}">
			</div>

			{{-- Enabled --}}
			<div class="form-group">
				<label class="control-label" for="{{ sprintf($childId, 'enabled') }}">{{{ trans('platform/menus::form.enabled') }}}</label>

				<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.enabled_help') }}}"></i>

				<div class="controls">
					<select data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, 'enabled') }}" id="{{ sprintf($childId, 'enabled') }}" class="form-control">
						<option value="1"{{ ( ! empty($child) ? $child->enabled : 1) == 1 ? ' selected="selected"' : null }}>{{{ trans('general.enabled') }}}</option>
						<option value="0"{{ ( ! empty($child) ? $child->enabled : 1) == 0 ? ' selected="selected"' : null }}>{{{ trans('general.disabled') }}}</option>
					</select>
				</div>
			</div>

			{{-- Parent --}}
			<div class="form-group">
				<label class="control-label" for="{{ sprintf($childId, 'parent') }}">{{{ trans('platform/menus::form.parent') }}}</label>

				<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.parent_help') }}}"></i>

				<div class="controls">
					<select data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" data-parents name="{{ sprintf($childName, 'parent') }}" id="{{ sprintf($childId, 'parent') }}" class="form-control"></select>
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
						<label class="control-label" for="{{ sprintf($childId, 'type') }}">{{{ trans('platform/menus::form.type') }}}</label>

						<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.type_help') }}}"></i>

						<div class="controls">
							<select data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, 'type') }}" id="{{ sprintf($childId, 'type') }}" class="form-control">
								<option value="static"{{ ( ! empty($child) ? $child->type : null) == 'static' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::form.types.static') }}}</option>
								<option value="page"{{ ( ! empty($child) ? $child->type : null) == 'page' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::form.types.page') }}}</option>
							</select>
						</div>
					</div>

				</div>

				<div class="col-md-6">

					{{-- Secure --}}
					<div class="form-group">
						<label class="control-label" for="{{ sprintf($childId, 'secure') }}">{{{ trans('platform/menus::form.secure') }}}</label>

						<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.secure_help') }}}"></i>

						<div class="controls">
							<select data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, 'secure') }}" id="{{ sprintf($childId, 'secure') }}" class="form-control">
								<option value="1"{{ ( ! empty($child) ? $child->secure : null) == 1 ? ' selected="selected"' : null }}>{{{ trans('general.yes') }}}</option>
								<option value="0"{{ ( ! empty($child) ? $child->secure : null) == 0 ? ' selected="selected"' : null }}>{{{ trans('general.no') }}}</option>
							</select>
						</div>
					</div>

				</div>

			</div>

			{{-- Static Uri --}}
			<div class="form-group">
				<label class="control-label" for="{{ sprintf($childId, 'static_uri') }}">{{{ trans('platform/menus::form.uri') }}}</label>

				<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.uri_help') }}}"></i>

				<input data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" type="text" name="{{ sprintf($childName, 'static_uri') }}" id="{{ sprintf($childId, 'uri') }}" class="form-control" value="{{ ! empty($child) ? $child->uri : null }}">
			</div>

		</fieldset>

	</div>

	<button type="button" class="btn btn-sm btn-info" data-toggle-options="{{{ ! empty($child) ? $child->id : 'new-child' }}}">More options</button>

	<span class="pull-right">
	@if ( ! empty($child))
	<button class="btn btn-sm btn-success" data-item-update="{{{ $child->id }}}">{{{ trans('button.update') }}}</button>
	<button class="btn btn-sm btn-danger" data-item-remove="{{{ $child->id }}}">{{{ trans('button.remove') }}}</button>
	@else
	<button class="btn btn-sm btn-success" data-item-create>{{{ trans('button.add') }}}</button>
	@endif
	</span>

	{{-- Options --}}
	<div class="hide" style="padding-top: 20px;" data-options>

		<div class="well well-md" style="border: none; border-radius: none; box-shadow: none;">

			<fieldset>

				<legend>{{{ trans('platform/menus::form.visibility') }}}</legend>

				{{-- Visibility --}}
				<div class="form-group">
					<label class="control-label" for="{{ sprintf($childId, 'visibility') }}">{{{ trans('platform/menus::form.visibility') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.visibility_help') }}}"></i>

					<div class="controls">
						<select data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" data-item-visibility="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, 'visibility') }}" id="{{ sprintf($childId, 'visibility') }}" class="form-control">
							<option value="always"{{ ( ! empty($child) ? $child->visibility : null) == 'always' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::form.visibilities.always') }}}</option>
							<option value="logged_in"{{ ( ! empty($child) ? $child->visibility : null) == 'logged_in' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::form.visibilities.logged_in') }}}</option>
							<option value="logged_out"{{ ( ! empty($child) ? $child->visibility : null) == 'logged_out' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::form.visibilities.logged_out') }}}</option>
							<option value="admin"{{ ( ! empty($child) ? $child->visibility : null) == 'admin' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::form.visibilities.admin') }}}</option>
						</select>
					</div>
				</div>

				{{-- Groups --}}
				<div class="form-group{{ ! in_array( ! empty($child) ? $child->visibility : null, array('logged_in', 'admin')) ? ' hide' : null }}" data-item-groups="{{{ ! empty($child) ? $child->id : 'new-child' }}}">
					<label class="control-label" for="{{ sprintf($childId, 'groups') }}">{{{ trans('platform/menus::form.groups') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.groups_help') }}}"></i>

					<div class="controls">
						<select data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, 'groups') }}" id="{{ sprintf($childId, 'groups') }}" class="form-control" multiple="true">
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
					<label class="control-label" for="{{ sprintf($childId, 'attribute_id') }}">{{{ trans('platform/menus::form.attributes.id') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.attributes.id_help') }}}"></i>

					<input data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" type="text" name="{{ sprintf($childName, 'attribute_id') }}" id="{{ sprintf($childId, 'attribute_id') }}" class="form-control" value="{{ ! empty($child) ? $child->attribute_id : null }}">
				</div>

				{{-- Class --}}
				<div class="form-group">
					<label class="control-label" for="{{ sprintf($childId, 'attribute_class') }}">{{{ trans('platform/menus::form.attributes.class') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.attributes.class_help') }}}"></i>

					<input data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" type="text" name="{{ sprintf($childName, 'attribute_class') }}" id="{{ sprintf($childId, 'attribute_class') }}" class="form-control" value="{{ ! empty($child) ? $child->attribute_class : null }}">
				</div>

				{{-- Name --}}
				<div class="form-group">
					<label class="control-label" for="{{ sprintf($childId, 'attribute_name') }}">{{{ trans('platform/menus::form.attributes.name') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.attributes.name_help') }}}"></i>

					<input data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" type="text" name="{{ sprintf($childName, 'attribute_name') }}" id="{{ sprintf($childId, 'attribute_name') }}" class="form-control" value="{{ ! empty($child) ? $child->attribute_name : null }}">
				</div>

				{{-- Title --}}
				<div class="form-group">
					<label class="control-label" for="{{ sprintf($childId, 'attribute_title') }}">{{{ trans('platform/menus::form.attributes.title') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.attributes.title_help') }}}"></i>

					<input data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" type="text" name="{{ sprintf($childName, 'attribute_title') }}" id="{{ sprintf($childId, 'attribute_title') }}" class="form-control" value="{{ ! empty($child) ? $child->attribute_title : null }}">
				</div>

				{{-- Target --}}
				<div class="form-group">
					<label class="control-label" for="{{ sprintf($childId, 'attribute_target') }}">{{{ trans('platform/menus::form.attributes.target') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.attributes.target_help') }}}"></i>

					<div class="controls">
						<select data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" name="{{ sprintf($childName, 'attribute_target') }}" id="{{ sprintf($childId, 'attribute_target') }}" class="form-control">
							<option value="self"{{ ( ! empty($child) ? $child->target : null) == 'self' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::form.attributes.targets.self') }}}</option>
							<option value="new_children"{{ ( ! empty($child) ? $child->target : null) == 'new_children' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::form.attributes.targets.blank') }}}</option>
							<option value="parent_frame"{{ ( ! empty($child) ? $child->target : null) == 'parent_frame' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::form.attributes.targets.parent') }}}</option>
							<option value="top_frame"{{ ( ! empty($child) ? $child->target : null) == 'top_frame' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::form.attributes.targets.top') }}}</option>
						</select>
					</div>
				</div>

			</fieldset>

		</div>

	</div>

</div>

@if ( ! empty($child) and $children = $child->getChildren())
@each('platform/menus::form', $children, 'child')
@endif
