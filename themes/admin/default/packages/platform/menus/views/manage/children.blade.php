<li data-item-id="{{ $child->id }}">
	<div class="item-handle"><i class="fa fa-reorder"></i></div>

	<div class="item-name{{ $child->enabled ? null : ' disabled' }}" data-item="{{ $child->id }}" data-item-name="{{ $child->id }}">{{ $child->name }}</div>

	<ol>
		@if ( ! empty($child) and $children = $child->getChildren())
			@each('platform/menus::manage/children', $children, 'child')
		@endif
	</ol>
</li>
