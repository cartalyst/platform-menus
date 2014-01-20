<li data-item-id="{{ $child->id }}">
	<div class="item-handle"><i class="fa fa-reorder"></i></div>

	<div class="item-name" data-item="{{ $child->id }}" data-item-name="{{ $child->id }}">
		{{ $child->name }}

		{{ $child->enabled ? '' : '<span class="item-status"><i class="fa fa-eye-slash"></i></span>' }}

	</div>

	<ol>
		@if ( ! empty($child) and $children = $child->getChildren())
			@each('platform/menus::manage/children', $children, 'child')
		@endif
	</ol>
</li>
