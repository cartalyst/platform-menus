<li data-item-id="{{ $child->id }}">
	<div class="item-dd-handle"></div>

	<div data-item="{{ $child->id }}" class="item-name">{{ $child->name }}</div>

	<ol>
		@if ( ! empty($child) and $children = $child->getChildren())
			@each('platform/menus::children', $children, 'child')
		@endif
	</ol>
</li>
