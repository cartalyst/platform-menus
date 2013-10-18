<li class="item" data-item-id="{{ $child->id }}">
	<div class="item-dd-handle">{{ trans('platform/menus::button.drag_children') }}</div>

	<div class="item-name">{{ $child->name }}</div>

	@if ($children = $child->getChildren())
	<ol class="items">
		@each('platform/menus::children', $children, 'child')
	</ol>
	@endif
</li>
