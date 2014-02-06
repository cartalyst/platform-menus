<li data-item-id="{{ $child->id }}">
	<div class="item-handle"><i class="fa fa-bars"></i></div>

	<div class="item-name" data-item="{{ $child->id }}" >
		<span data-item-name="{{ $child->id }}">{{ $child->name }}</span>

		<span class="item-status{{ $child->enabled == 0 ? null : ' hide' }}" data-item-status="{{ $child->id }}"><i class="fa fa-eye-slash"></i></span>
	</div>

	<ol>
		@if ( ! empty($child) and $children = $child->getChildren())
			@each('platform/menus::manage/children', $children, 'child')
		@endif
	</ol>
</li>
