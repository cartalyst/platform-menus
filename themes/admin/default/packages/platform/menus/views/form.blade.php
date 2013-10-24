<?php
	$childId   = ! empty($child) ? $child->id : 'new-child';
	$childName = ! empty($child) ? "children[{$child->id}][%s]" : 'new-child_%s';
	$segment = ! empty($child) ? 'edit' : 'create';
	$parentId = ( ! empty($child) and $child->depth > 1) ? $child->getParent()->id : 0;
?>
<div class="well well-md hide" style="background: #fff" data-item-form="{{{ $childId }}}" data-item-parent="{{{ $parentId }}}">

	<input type="hidden" id="{{ $childId }}_current_slug" value="{{ ! empty($child) ? $child->slug : null }}">

	<h4>
		{{{ trans("platform/menus::form.{$segment}.legend") }}}

		<span class="pull-right"><small class="item-close" data-item-close="{{{ $childId }}}">&times;</small></span>
	</h4>

	<p>{{{ trans("platform/menus::form.{$segment}.description") }}}</p>

	{{-- Item Details --}}
	<div class="well well-md" style="border: none; border-radius: none; box-shadow: none;">

		<fieldset>

			<legend>Item details</legend>

			{{-- Item Name --}}
			<div class="form-group">
				<label class="control-label" for="{{ $childId }}_name">{{{ trans('platform/menus::form.name') }}}</label>

				<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.name_help') }}}"></i>

				<input type="text" name="{{ sprintf($childName, 'name') }}" id="{{ $childId }}_name" class="form-control" value="{{ ! empty($child) ? $child->name : null }}">
			</div>

			{{-- Item Slug --}}
			<div class="form-group">
				<label class="control-label" for="{{ $childId }}_slug">{{{ trans('platform/menus::form.slug') }}}</label>

				<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.slug_help') }}}"></i>

				<input type="text" name="{{ sprintf($childName, 'slug') }}" id="{{ $childId }}_slug" class="form-control" value="{{ ! empty($child) ? $child->slug : null }}">
			</div>

			{{-- Enabled --}}
			<div class="form-group">
				<label class="control-label" for="{{ $childId }}_enabled">{{{ trans('platform/menus::form.enabled') }}}</label>

				<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.enabled_help') }}}"></i>

				<div class="controls">
					<select name="{{ sprintf($childName, 'enabled') }}" id="{{ $childId }}_enabled" class="form-control">
						<option value="1"{{ ( ! empty($child) ? $child->enabled : 1) == 1 ? ' selected="selected"' : null }}>{{{ trans('general.enabled') }}}</option>
						<option value="0"{{ ( ! empty($child) ? $child->enabled : 1) == 0 ? ' selected="selected"' : null }}>{{{ trans('general.disabled') }}}</option>
					</select>
				</div>
			</div>

			{{-- Parent --}}
			<div class="form-group">
				<label class="control-label" for="{{ $childId }}_enabled">Parent</label>

				<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.parent_help') }}}"></i>

				<div class="controls">
					<select data-parents class="form-control"></select>
				</div>
			</div>

		</fieldset>

	</div>

	{{-- Item URL --}}
	<div class="well well-md" style="border: none; border-radius: none; box-shadow: none;">

		<fieldset>

			<legend>Item URL</legend>

			<div class="row">

				<div class="col-md-4">

					{{-- Item Type --}}
					<div class="form-group">
						<label class="control-label" for="{{ $childId }}_type">{{{ trans('platform/menus::form.type.title') }}}</label>

						<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.type_help') }}}"></i>

						<div class="controls">
							<select name="{{ sprintf($childName, 'type') }}" id="{{ $childId }}_type" class="form-control">
								<option value="static"{{ ( ! empty($child) ? $child->type : null) == 'static' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::form.type.static') }}}</option>
								<option value="page"{{ ( ! empty($child) ? $child->type : null) == 'page' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::form.type.page') }}}</option>
							</select>
						</div>
					</div>

				</div>

				<div class="col-md-8">

					{{-- Item Uri --}}
					<div class="form-group">
						<label class="control-label" for="{{ $childId }}_uri">{{{ trans('platform/menus::form.uri') }}}</label>

						<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.uri_help') }}}"></i>

						<input type="text" name="{{ sprintf($childName, 'uri') }}" id="{{ $childId }}_uri" class="form-control" value="{{ ! empty($child) ? $child->uri : null }}">
					</div>

				</div>

			</div>

			<div class="row">

				<div class="col-md-4">

					{{-- Secure --}}
					<div class="form-group">
						<label class="control-label" for="{{ $childId }}_secure">{{{ trans('platform/menus::form.secure') }}}</label>

						<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.secure_help') }}}"></i>

						<div class="controls">
							<select name="{{ sprintf($childName, 'secure') }}" id="{{ $childId }}_secure" class="form-control">
								<option value="1"{{ ( ! empty($child) ? $child->secure : null) == 1 ? ' selected="selected"' : null }}>{{{ trans('general.yes') }}}</option>
								<option value="0"{{ ( ! empty($child) ? $child->secure : null) == 0 ? ' selected="selected"' : null }}>{{{ trans('general.no') }}}</option>
							</select>
						</div>
					</div>

				</div>

			</div>

		</fieldset>

	</div>

	<button type="button" class="btn btn-sm btn-info" data-toggle-options="{{{ $childId }}}">More options</button>

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
					<label class="control-label" for="{{ $childId }}_visibility">{{{ trans('platform/menus::form.visibility') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.visibility_help') }}}"></i>

					<div class="controls">
						<select name="{{ sprintf($childName, 'visibility') }}" id="{{ $childId }}_visibility" class="form-control">
							<option value="always"{{ ( ! empty($child) ? $child->visibility : null) == 'always' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::form.visibilities.always') }}}</option>
							<option value="logged_in"{{ ( ! empty($child) ? $child->visibility : null) == 'logged_in' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::form.visibilities.logged_in') }}}</option>
							<option value="logged_out"{{ ( ! empty($child) ? $child->visibility : null) == 'logged_out' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::form.visibilities.logged_out') }}}</option>
							<option value="admin"{{ ( ! empty($child) ? $child->visibility : null) == 'admin' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::form.visibilities.admin') }}}</option>
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
					<label class="control-label" for="{{ $childId }}_attribute_id">{{{ trans('platform/menus::form.attributes.id') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.attributes.id_help') }}}"></i>

					<input type="text" name="{{ sprintf($childName, 'attribute_id') }}" id="{{ $childId }}_attribute_id" class="form-control" value="{{ ! empty($child) ? $child->attribute_id : null }}">
				</div>

				{{-- Name --}}
				<div class="form-group">
					<label class="control-label" for="{{ $childId }}_attribute_name">{{{ trans('platform/menus::form.attributes.name') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.attributes.name_help') }}}"></i>

					<input type="text" name="{{ sprintf($childName, 'attribute_name') }}" id="{{ $childId }}_attribute_name" class="form-control" value="{{ ! empty($child) ? $child->attribute_name : null }}">
				</div>

				{{-- Class --}}
				<div class="form-group">
					<label class="control-label" for="{{ $childId }}_attribute_class">{{{ trans('platform/menus::form.attributes.class') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.attributes.class_help') }}}"></i>

					<input type="text" name="{{ sprintf($childName, 'attribute_class') }}" id="{{ $childId }}_attribute_class" class="form-control" value="{{ ! empty($child) ? $child->attribute_class : null }}">
				</div>

				{{-- Title --}}
				<div class="form-group">
					<label class="control-label" for="{{ $childId }}_attribute_title">{{{ trans('platform/menus::form.attributes.title') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.attributes.title_help') }}}"></i>

					<input type="text" name="{{ sprintf($childName, 'attribute_title') }}" id="{{ $childId }}_attribute_title" class="form-control" value="{{ ! empty($child) ? $child->attribute_title : null }}">
				</div>

				{{-- Target --}}
				<div class="form-group">
					<label class="control-label" for="{{ $childId }}_attribute_target">{{{ trans('platform/menus::form.attributes.target.title') }}}</label>

					<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.attributes.target_help') }}}"></i>

					<div class="controls">
						<select name="{{ sprintf($childName, 'attribute_target') }}" id="{{ $childId }}_attribute_target" class="form-control">
							<option value="self"{{ ( ! empty($child) ? $child->target : null) == 'self' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::form.attributes.target.self') }}}</option>
							<option value="new_children"{{ ( ! empty($child) ? $child->target : null) == 'new_children' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::form.attributes.target.blank') }}}</option>
							<option value="parent_frame"{{ ( ! empty($child) ? $child->target : null) == 'parent_frame' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::form.attributes.target.parent') }}}</option>
							<option value="top_frame"{{ ( ! empty($child) ? $child->target : null) == 'top_frame' ? ' selected="selected"' : null }}>{{{ trans('platform/menus::form.attributes.target.top') }}}</option>
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
