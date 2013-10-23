<li class="item"{{ empty($child) ? ' data-template style="display: none;"' : null }}>
	<div class="item-dd-handle"></div>

	<div data-item="{{ ! empty($child) ? $child->id : '[[ slug ]]' }}" class="item-name">{{ ! empty($child) ? $child->name : '[[ name ]]' }}</div>

	@if ( ! empty($child) and $children = $child->getChildren())
	<ol class="items">
		@each('platform/menus::children', $children, 'child')
	</ol>
	@endif
</li>
