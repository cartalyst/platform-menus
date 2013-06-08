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

				@include('platform/menus::children-form')
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
