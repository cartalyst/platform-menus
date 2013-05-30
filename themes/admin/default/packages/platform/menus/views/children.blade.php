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
					<input type="text" data-children="{{ $child->id }}" name="children[{{ $child->id }}][name]" id="{{ $child->id }}_name" class="input-block-level" value="{{ $child->name }}" placeholder="">
				</div>

				{{-- Slug --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->id }}_slug">@lang('platform/menus::form.child.slug')</label>
					<input type="text" data-children="{{ $child->id }}" name="children[{{ $child->id }}][slug]" id="{{ $child->id }}_slug" class="input-block-level" value="{{ $child->slug }}" placeholder="">
				</div>

				{{-- Item Type --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->id }}-type">@lang('platform/menus::form.child.type.title')</label>
					<div class="controls">
						<select data-children="{{ $child->id }}" name="children[{{ $child->id }}][type]" id="{{ $child->id }}_type" class="input-block-level">
							<option value="static"{{ $child->type == 'static' ? ' selected="selected"' : '' }}>@lang('platform/menus::form.child.type.static')</option>
							<option value="page"{{ $child->type == 'page' ? ' selected="selected"' : '' }}>@lang('platform/menus::form.child.type.page')</option>
						</select>
					</div>
				</div>

				{{-- Item Uri --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->id }}_uri">@lang('platform/menus::form.child.uri')</label>
					<div class="input-prepend">
						<span class="add-on">{{ str_finish(URL::to('/'), '/') }}</span>
						<input type="text" data-children="{{ $child->id }}" name="children[{{ $child->id }}][uri]" id="{{ $child->id }}_uri" class="input-block-level" value="{{ $child->uri }}" placeholder="">
					</div>
				</div>

				{{-- Visibility --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->id }}-visibility">@lang('platform/menus::form.child.visibility.title')</label>
					<div class="controls">
						<select data-children="{{ $child->id }}" name="children[{{ $child->id }}][visibility]" id="{{ $child->id }}_visibility" class="input-block-level">
							<option value="always"{{ $child->visibility == 'always' ? ' selected="selected"' : '' }}>@lang('platform/menus::form.child.visibility.always')</option>
							<option value="logged_in"{{ $child->visibility == 'logged_in' ? ' selected="selected"' : '' }}>@lang('platform/menus::form.child.visibility.logged_in')</option>
							<option value="logged_out"{{ $child->visibility == 'logged_out' ? ' selected="selected"' : '' }}>@lang('platform/menus::form.child.visibility.logged_out')</option>
							<option value="admin"{{ $child->visibility == 'admin' ? ' selected="selected"' : '' }}>@lang('platform/menus::form.child.visibility.admin')</option>
						</select>
					</div>
				</div>

				{{-- Secure --}}
				<div class="control-group">
					<label class="control-label" for="new-child-secure">@lang('platform/menus::form.child.secure')</label>
					<div class="controls">
						<select data-children="{{ $child->id }}" name="children[{{ $child->id }}][secure]" id="{{ $child->id }}_secure" class="input-block-level">
							<option value="1"{{ $child->secure == 1 ? ' selected="selected"' : '' }}>@lang('general.yes')</option>
							<option value="0"{{ $child->secure == 0 ? ' selected="selected"' : '' }}>@lang('general.no')</option>
						</select>
					</div>
				</div>

				{{-- Target --}}
				<div class="control-group">
					<label class="control-label" for="{{ $child->id }}_target">@lang('platform/menus::form.child.target.title')</label>
					<div class="controls">
						<select data-children="{{ $child->id }}" name="children[{{ $child->id }}][target]" id="{{ $child->id }}_target" class="input-block-level">
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
					<input type="text" data-children="{{ $child->id }}" name="children[{{ $child->id }}][class]" id="{{ $child->id }}_class" class="input-block-level" value="{{ $child->class }}" placeholder="">
				</div>

				{{-- Enabled --}}
				<div class="control-group">
					<label class="control-label" for="new-child-enabled">@lang('platform/menus::form.child.enabled')</label>
					<div class="controls">
						<select data-children="{{ $child->id }}" name="children[{{ $child->id }}][enabled]" id="{{ $child->id }}_enabled" class="input-block-level">
							<option value="1"{{ $child->enabled == 1 ? ' selected="selected"' : '' }}>@lang('general.enabled')</option>
							<option value="0"{{ $child->enabled == 0 ? ' selected="selected"' : '' }}>@lang('general.disabled')</option>
						</select>
					</div>
				</div>
			</fieldset>
		</div>

		<div class="modal-footer">
			<button name="remove" class="remove btn btn-mini btn-primary" data-dismiss="modal" aria-hidden="true">@lang('platform/menus::button.remove_child')</button>
			<button type="button" class="btn btn-medium" data-dismiss="modal" aria-hidden="true">*Update Children</button>
		</div>

	</div>

	@if ($children = $child->getChildren())
	<ol class="items">
		@each('platform/menus::children', $children, 'child')
	</ol>
	@endif
</li>


<!-- this is the same as above, need to use tempo to render the menus item aswell! -->
<li data-template class="item" data-slug="[[ slug ]]" style="display: none;">
	<div class="item-dd-handle">@lang('platform/menus::button.drag_children')</div>

	<div href="#item-details-[[ slug ]]" class="item-toggle" data-toggle="modal">@lang('platform/menus::button.toggle_child_details')</div>

	<div class="item-name">[[ name ]]</div>

	<div id="item-details-[[ slug ]]" class="modal hide fade">

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>@lang('platform/menus::form.child.update.legend', array('menu' => '[[ name ]]'))</h3>
		</div>

		<div class="modal-body">
			<fieldset id="item-details">

				<input type="hidden" name="children[[[ slug ]]][id]" value="[[ slug ]]">

				{{-- Name --}}
				<div class="control-group">
					<label class="control-label" for="[[ slug ]]_name">@lang('platform/menus::form.child.name')</label>
					<input type="text" data-children="[[ slug ]]" name="children[[[ slug ]]][name]" id="[[ slug ]]_name" class="input-block-level" value="[[ name ]]" placeholder="">
				</div>

				{{-- Slug --}}
				<div class="control-group">
					<label class="control-label" for="[[ slug ]]_slug">@lang('platform/menus::form.child.slug')</label>
					<input type="text" data-children="[[ slug ]]" name="children[[[ slug ]]][slug]" id="[[ slug ]]_slug" class="input-block-level" value="[[ slug ]]" placeholder="">
				</div>

				{{-- Item Type --}}
				<div class="control-group">
					<label class="control-label" for="[[ slug ]]-type">@lang('platform/menus::form.child.type.title')</label>
					<div class="controls">
						<select data-children="[[ slug ]]" name="children[[[ slug ]]][type]" id="[[ slug ]]_type" class="input-block-level">
							<option value="static" [? if type == 'static' ?] selected="selected" [? endif ?]>@lang('platform/menus::form.child.type.static')</option>
							<option value="page" [? if type == 'page' ?] selected="selected" [? endif ?]>@lang('platform/menus::form.child.type.page')</option>
						</select>
					</div>
				</div>

				{{-- Item Uri --}}
				<div class="control-group">
					<label class="control-label" for="[[ slug ]]_uri">@lang('platform/menus::form.child.uri')</label>
					<div class="input-prepend">
						<span class="add-on">{{ str_finish(URL::to('/'), '/') }}</span>
						<input type="text" data-children="[[ slug ]]" name="children[[[ slug ]]][uri]" id="[[ slug ]]_uri" class="input-block-level" value="[[ uri ]]" placeholder="">
					</div>
				</div>

				{{-- Visibility --}}
				<div class="control-group">
					<label class="control-label" for="[[ slug ]]-visibility">@lang('platform/menus::form.child.visibility.title')</label>
					<div class="controls">
						<select data-children="[[ slug ]]" name="children[[[ slug ]]][visibility]" id="[[ slug ]]_visibility" class="input-block-level">
							<option value="always">@lang('platform/menus::form.child.visibility.always')</option>
							<option value="logged_in">@lang('platform/menus::form.child.visibility.logged_in')</option>
							<option value="logged_out">@lang('platform/menus::form.child.visibility.logged_out')</option>
							<option value="admin">@lang('platform/menus::form.child.visibility.admin')</option>
						</select>
					</div>
				</div>

				{{-- Secure --}}
				<div class="control-group">
					<label class="control-label" for="new-child-secure">@lang('platform/menus::form.child.secure')</label>
					<div class="controls">
						<select data-children="[[ slug ]]" name="children[[[ slug ]]][secure]" id="[[ slug ]]_secure" class="input-block-level">
							<option value="1">@lang('general.yes')</option>
							<option value="0">@lang('general.no')</option>
						</select>
					</div>
				</div>

				{{-- Target --}}
				<div class="control-group">
					<label class="control-label" for="[[ slug ]]_target">@lang('platform/menus::form.child.target.title')</label>
					<div class="controls">
						<select data-children="[[ slug ]]" name="children[[[ slug ]]][target]" id="[[ slug ]]_target" class="input-block-level">
							<option value="self">@lang('platform/menus::form.child.target.self')</option>
							<option value="new_children">@lang('platform/menus::form.child.target.blank')</option>
							<option value="parent_frame">@lang('platform/menus::form.child.target.parent')</option>
							<option value="top_frame">@lang('platform/menus::form.child.target.top')</option>
						</select>
					</div>
				</div>

				{{-- CSS Class --}}
				<div class="control-group">
					<label class="control-label" for="[[ slug ]]_class">@lang('platform/menus::form.child.class')</label>
					<input type="text" data-children="[[ slug ]]" name="children[[[ slug ]]][class]" id="[[ slug ]]_class" class="input-block-level" value="[[ css_class ]]" placeholder="">
				</div>

				{{-- Enabled --}}
				<div class="control-group">
					<label class="control-label" for="new-child-enabled">@lang('platform/menus::form.child.enabled')</label>
					<div class="controls">
						<select data-children="[[ slug ]]" name="children[[[ slug ]]][enabled]" id="[[ slug ]]_enabled" class="input-block-level">
							<option value="1">@lang('general.enabled')</option>
							<option value="0">@lang('general.disabled')</option>
						</select>
					</div>
				</div>
			</fieldset>
		</div>

		<div class="modal-footer">
			<button name="remove" class="remove btn btn-mini btn-primary" data-dismiss="modal" aria-hidden="true">@lang('platform/menus::button.remove_child')</button>
			<button type="button" class="btn btn-medium" data-dismiss="modal" aria-hidden="true">*Update Children</button>
		</div>

	</div>
</li>
