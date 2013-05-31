<li class="item" data-id="{{ $child->id }}">
	<div class="item-dd-handle">{{ trans('platform/menus::button.drag_children') }}</div>

	<div href="#item-details-{{ $child->id }}" class="item-toggle" data-toggle="modal">{{ trans('platform/menus::button.toggle_child_details') }}</div>

	<div class="item-name">{{ $child->name }}</div>

	<div id="item-details-{{ $child->id }}" class="modal hide fade">

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>{{ trans('platform/menus::form.child.update.legend', array('menu' => $child->name)) }}</h3>
		</div>

		<div class="modal-body">
			<fieldset id="item-details">
				<input type="hidden" name="children[{{ $child->id }}][id]" value="{{ $child->id }}">

				{{-- Name --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->id }}_name">{{ trans('platform/menus::form.child.name') }}</label>
					<input type="text" data-children="{{ $child->id }}" name="children[{{ $child->id }}][name]" id="{{ $child->id }}_name" class="input-block-level" value="{{ $child->name }}" placeholder="">
				</div>

				{{-- Slug --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->id }}_slug">{{ trans('platform/menus::form.child.slug') }}</label>
					<input type="text" data-children="{{ $child->id }}" name="children[{{ $child->id }}][slug]" id="{{ $child->id }}_slug" class="input-block-level" value="{{ $child->slug }}" placeholder="">
				</div>

				{{-- Item Type --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->id }}-type">{{ trans('platform/menus::form.child.type.title') }}</label>
					<div class="controls">
						<select data-children="{{ $child->id }}" name="children[{{ $child->id }}][type]" id="{{ $child->id }}_type" class="input-block-level">
							<option value="static"{{ $child->type == 'static' ? ' selected="selected"' : '' }}>{{ trans('platform/menus::form.child.type.static') }}</option>
							<option value="page"{{ $child->type == 'page' ? ' selected="selected"' : '' }}>{{ trans('platform/menus::form.child.type.page') }}</option>
						</select>
					</div>
				</div>

				{{-- Item Uri --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->id }}_uri">{{ trans('platform/menus::form.child.uri') }}</label>
					<div class="input-prepend">
						<span class="add-on">{{ str_finish(URL::to('/'), '/') }}</span>
						<input type="text" data-children="{{ $child->id }}" name="children[{{ $child->id }}][uri]" id="{{ $child->id }}_uri" class="input-block-level" value="{{ $child->uri }}" placeholder="">
					</div>
				</div>

				{{-- Visibility --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->id }}-visibility">{{ trans('platform/menus::form.child.visibility.title') }}</label>
					<div class="controls">
						<select data-children="{{ $child->id }}" name="children[{{ $child->id }}][visibility]" id="{{ $child->id }}_visibility" class="input-block-level">
							<option value="always"{{ $child->visibility == 'always' ? ' selected="selected"' : '' }}>{{ trans('platform/menus::form.child.visibility.always') }}</option>
							<option value="logged_in"{{ $child->visibility == 'logged_in' ? ' selected="selected"' : '' }}>{{ trans('platform/menus::form.child.visibility.logged_in') }}</option>
							<option value="logged_out"{{ $child->visibility == 'logged_out' ? ' selected="selected"' : '' }}>{{ trans('platform/menus::form.child.visibility.logged_out') }}</option>
							<option value="admin"{{ $child->visibility == 'admin' ? ' selected="selected"' : '' }}>{{ trans('platform/menus::form.child.visibility.admin') }}</option>
						</select>
					</div>
				</div>

				{{-- Secure --}}
				<div class="control-group">
					<label class="control-label" for="new-child-secure">{{ trans('platform/menus::form.child.secure') }}</label>
					<div class="controls">
						<select data-children="{{ $child->id }}" name="children[{{ $child->id }}][secure]" id="{{ $child->id }}_secure" class="input-block-level">
							<option value="1"{{ $child->secure == 1 ? ' selected="selected"' : '' }}>{{ trans('general.yes') }}</option>
							<option value="0"{{ $child->secure == 0 ? ' selected="selected"' : '' }}>{{ trans('general.no') }}</option>
						</select>
					</div>
				</div>

				{{-- Target --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->id }}_target">{{ trans('platform/menus::form.child.target.title') }}</label>
					<div class="controls">
						<select data-children="{{ $child->id }}" name="children[{{ $child->id }}][target]" id="{{ $child->id }}_target" class="input-block-level">
							<option value="self"{{ $child->target == 'self' ? ' selected="selected"' : '' }}>{{ trans('platform/menus::form.child.target.self') }}</option>
							<option value="new_children"{{ $child->target == 'new_children' ? ' selected="selected"' : '' }}>{{ trans('platform/menus::form.child.target.blank') }}</option>
							<option value="parent_frame"{{ $child->target == 'parent_frame' ? ' selected="selected"' : '' }}>{{ trans('platform/menus::form.child.target.parent') }}</option>
							<option value="top_frame"{{ $child->target == 'top_frame' ? ' selected="selected"' : '' }}>{{ trans('platform/menus::form.child.target.top') }}</option>
						</select>
					</div>
				</div>

				{{-- CSS Class --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->id }}_class">{{ trans('platform/menus::form.child.class') }}</label>
					<input type="text" data-children="{{ $child->id }}" name="children[{{ $child->id }}][class]" id="{{ $child->id }}_class" class="input-block-level" value="{{ $child->class }}" placeholder="">
				</div>

				{{-- Enabled --}}
				<div class="control-group">
					<label class="control-label" for="new-child-enabled">{{ trans('platform/menus::form.child.enabled') }}</label>
					<div class="controls">
						<select data-children="{{ $child->id }}" name="children[{{ $child->id }}][enabled]" id="{{ $child->id }}_enabled" class="input-block-level">
							<option value="1"{{ $child->enabled == 1 ? ' selected="selected"' : '' }}>{{ trans('general.enabled') }}</option>
							<option value="0"{{ $child->enabled == 0 ? ' selected="selected"' : '' }}>{{ trans('general.disabled') }}</option>
						</select>
					</div>
				</div>
			</fieldset>
		</div>

		<div class="modal-footer">
			<button name="remove" class="remove btn btn-mini btn-primary" data-dismiss="modal" aria-hidden="true">{{ trans('platform/menus::button.remove_child') }}</button>
			<button type="button" class="btn btn-medium" data-dismiss="modal" aria-hidden="true">{{ trans('platform/menus::button.update_child') }}</button>
		</div>

	</div>

	@if ($children = $child->getChildren())
	<ol class="items">
		@each('platform/menus::children', $children, 'child')
	</ol>
	@endif
</li>
