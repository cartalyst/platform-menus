<li class="item" data-slug="{{ $child->slug }}">
	<div class="item-dd-handle">@lang('platform/menus::button.drag_children')</div>

	<div href="#item-details-{{ $child->slug }}" class="item-toggle" data-toggle="modal">@lang('platform/menus::button.toggle_child_details')</div>

	<div class="item-name">{{ $child->name }}</div>

	<div id="item-details-{{ $child->slug }}" class="modal hide fade">

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>@lang('platform/menus::form.child.update.legend', array('menu' => $child->name))</h3>
		</div>

		<div class="modal-body">
			<fieldset id="item-details">

				{{-- Name --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->slug }}_name">@lang('platform/menus::form.child.name')</label>
					<input type="text" name="children[{{ $child->slug }}][name]" id="{{ $child->slug }}_name" class="input-block-level" value="{{ $child->name }}" placeholder="">
				</div>

				{{-- Slug --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->slug }}_slug">@lang('platform/menus::form.child.slug')</label>
					<input type="text" name="children[{{ $child->slug }}][slug]" id="{{ $child->slug }}_slug" class="input-block-level" value="{{ $child->slug }}" placeholder="">
				</div>

				{{-- Item Uri --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->slug }}_uri">@lang('platform/menus::form.child.uri')</label>
					<input type="text" name="children[{{ $child->slug }}][uri]" id="{{ $child->slug }}_uri" class="input-block-level" value="{{ $child->uri }}" placeholder="">
				</div>

				{{-- Target --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->slug }}_target">@lang('platform/menus::form.child.target.title')</label>
					<div class="controls">
						<select name="children[{{ $child->slug }}][target]" id="{{ $child->slug }}_target" class="input-block-level">
							<option value="0"{{ $child->target == 0 ? ' selected="selected"' : '' }}>@lang('platform/menus::form.child.target.self')</option>
							<option value="1"{{ $child->target == 1 ? ' selected="selected"' : '' }}>@lang('platform/menus::form.child.target.blank')</option>
							<option value="2"{{ $child->target == 2 ? ' selected="selected"' : '' }}>@lang('platform/menus::form.child.target.parent')</option>
							<option value="3"{{ $child->target == 3 ? ' selected="selected"' : '' }}>@lang('platform/menus::form.child.target.top')</option>
						</select>
					</div>
				</div>

				{{-- CSS Class --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->slug }}_class">@lang('platform/menus::form.child.class')</label>
					<input type="text" name="children[{{ $child->slug }}][class]" id="{{ $child->slug }}_class" class="input-block-level" value="{{ $child->class }}" placeholder="">
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
