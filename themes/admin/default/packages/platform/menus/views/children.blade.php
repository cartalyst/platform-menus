<li class="item" data-id="{{ $child->id }}">
	<div class="item-dd-handle">@lang('platform/menus::button.drag_children')</div>

	<div href="#item-details-{{ $child->id }}" class="item-toggle" data-toggle="modal">@lang('platform/menus::button.toggle_child_details')</div>

	<div class="item-name">{{ $child->name }}</div>

	<div id="item-details-{{ $child->id }}" class="modal hide fade">

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>@lang('platform/menus::form.child.update.legend', array('menu' => $child->name))</h3>
		</div>

		<div class="modal-body">
			<fieldset id="item-details">

				<input type="hidden" name="children[{{ $child->id }}][id]" value="{{ $child->id }}">

				{{-- Name --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->id }}_name">@lang('platform/menus::form.child.name')</label>
					<input type="text" name="children[{{ $child->id }}][name]" id="{{ $child->id }}_name" class="input-block-level" value="{{ $child->name }}" placeholder="">
				</div>

				{{-- Slug --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->id }}_slug">@lang('platform/menus::form.child.slug')</label>
					<input type="text" name="children[{{ $child->id }}][slug]" id="{{ $child->id }}_slug" class="input-block-level" value="{{ $child->slug }}" placeholder="">
				</div>

				{{-- Item Uri --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->id }}_uri">@lang('platform/menus::form.child.uri')</label>
					<div class="input-prepend">
						<span class="add-on">{{ str_finish(URL::to('/'), '/') }}</span>
						<input type="text" name="children[{{ $child->id }}][uri]" id="{{ $child->id }}_uri" class="input-block-level" value="{{ $child->uri }}" placeholder="">
					</div>
				</div>

				{{-- Target --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->id }}_target">@lang('platform/menus::form.child.target.title')</label>
					<div class="controls">
						<select name="children[{{ $child->id }}][target]" id="{{ $child->id }}_target" class="input-block-level">
							<option value="self"{{ $child->target == 'self' ? ' selected="selected"' : '' }}>@lang('platform/menus::form.child.target.self')</option>
							<option value="new_children"{{ $child->target == 'new_children' ? ' selected="selected"' : '' }}>@lang('platform/menus::form.child.target.blank')</option>
							<option value="parent_frame"{{ $child->target == 'parent_frame' ? ' selected="selected"' : '' }}>@lang('platform/menus::form.child.target.parent')</option>
							<option value="top_frame"{{ $child->target == 'top_frame' ? ' selected="selected"' : '' }}>@lang('platform/menus::form.child.target.top')</option>
						</select>
					</div>
				</div>

				{{-- CSS Class --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->id }}_class">@lang('platform/menus::form.child.class')</label>
					<input type="text" name="children[{{ $child->id }}][class]" id="{{ $child->id }}_class" class="input-block-level" value="{{ $child->class }}" placeholder="">
				</div>
			</fieldset>
		</div>

		<div class="modal-footer">
			<button type="button" class="btn btn-medium" data-dismiss="modal" aria-hidden="true">@lang('button.close')</button>
			<button name="remove" class="remove btn btn-medium btn-primary" data-dismiss="modal" aria-hidden="true">@lang('platform/menus::button.remove_child')</button>
		</div>

	</div>

	@if ($children = $child->getChildren())
		<ol class="items">
			@each('platform/menus::children', $children, 'child')
		</ol>
	@endif
</li>
