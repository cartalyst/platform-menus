<li data-item-id="{{ $child->id }}" class="item">
	<div class="item-dd-handle"></div>

	<div data-item="{{ $child->id }}" class="item-name">{{ $child->name }}</div>

	<ol class="items">
		@if ( ! empty($child) and $children = $child->getChildren())
			@each('platform/menus::children', $children, 'child')
		@endif
	</ol>
</li>
