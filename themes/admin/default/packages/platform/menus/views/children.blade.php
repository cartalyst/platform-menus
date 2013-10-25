<li data-item-id="{{ ! empty($child) ? $child->id : '[[ slug ]]' }}" class="item"{{ empty($child) ? ' data-template style="display: none;"' : null }}>
	<div class="item-dd-handle"></div>

	<div data-item="{{ ! empty($child) ? $child->id : '[[ slug ]]' }}" class="item-name">{{ ! empty($child) ? $child->name : '[[ name ]]' }}</div>

	<ol class="items">
		@if ( ! empty($child) and $children = $child->getChildren())
			@each('platform/menus::children', $children, 'child')
		@endif
	</ol>
</li>
