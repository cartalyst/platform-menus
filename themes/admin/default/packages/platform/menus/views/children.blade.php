<ol class="items">
@if ( ! empty($children))
@foreach ($children as $child)
	<li class="item" data-slug="{{ $child->slug }}">
		<div class="item-dd-handle">Drag</div>

		<div href="#item-details-{{ $child->slug }}" class="item-toggle" data-toggle="modal">Toggle Details</div>

		<div class="item-name">{{ $child->name }}</div>

		<div id="item-details-{{ $child->slug }}" class="modal hide fade">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>{{ $child->name }} Details</h3>
			</div>

			<div class="modal-body">
				<fieldset id="item-details">
					<!-- Children Name -->
					<div class="control-group">
						<label class="control-label" for="{{ $child->slug }}_name">Name</label>
						<input type="text" name="children[{{ $child->slug }}][name]" id="{{ $child->slug }}_name" class="input-block-level" value="{{ $child->name }}" placeholder="">
					</div>

					<!--  Children Slug -->
					<div class="control-group">
						<label class="control-label" for="{{ $child->slug }}_slug">Slug</label>
						<input type="text" name="children[{{ $child->slug }}][slug]" id="{{ $child->slug }}_slug" class="input-block-level" value="{{ $child->slug }}" placeholder="">
					</div>

					<!-- Target -->
					<div class="control-group">
						<label class="control-label" for="{{ $child->slug }}_target">Target</label>
						<div class="controls">
							<select name="children[{{ $child->slug }}][target]" id="{{ $child->slug }}_target" class="child-target">
								<option value="0"{{ $child->target == 0 ? ' selected="selected"' : '' }}>Same Window</option>
								<option value="1"{{ $child->target == 1 ? ' selected="selected"' : '' }}>New Window</option>
								<option value="2"{{ $child->target == 2 ? ' selected="selected"' : '' }}>Parent Frame</option>
								<option value="3"{{ $child->target == 3 ? ' selected="selected"' : '' }}>Top Frame (Main Document)</option>
							</select>
						</div>
					</div>

					<!-- CSS Class -->
					<div class="control-group">
						<label class="control-label" for="{{ $child->slug }}_class">CSS Class</label>
						<input type="text" name="children[{{ $child->slug }}][class]" id="{{ $child->slug }}_class" class="input-block-level" value="{{ $child->class }}" placeholder="">
					</div>
				</fieldset>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-large" data-dismiss="modal" aria-hidden="true">@lang('button.close')</button>
				<button name="remove" class="remove btn btn-large btn-primary" data-dismiss="modal" aria-hidden="true">@lang('button.delete')</button>
			</div>

		</div>

	@if ($children = $child->getChildren())
		@include('platform/menus::children', compact('children'))
	@endif
	</li>
@endforeach
@endif
</ol>
