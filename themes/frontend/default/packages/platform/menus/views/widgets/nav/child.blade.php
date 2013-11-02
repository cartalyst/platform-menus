<?php
$children = $child->getChildren();
$isSubmenu = ($children and $child->depth > 1);
?>
<li class="{{ $child->isActive ? 'active' : null }} dropdown{{ $isSubmenu ? '-submenu' : null }}">
	<a target="{{ $child->target }}" href="{{ $child->uri }}"@if ($children) id="drop-{{ $child->slug }}" role="button" class="dropdown-toggle" data-toggle="dropdown"@endif>
		<i class="{{ $child->class }}"></i>
		<span>{{ $child->name }}</span>
		@if ($children and ! $isSubmenu)
		<b class="caret"></b>
		@endif
	</a>

	@if ($children)
		<ul class="dropdown-menu" role="menu" aria-labelledby="drop-{{ $child->slug }}">
		@each('platform/menus::widgets/nav/child', $children, 'child')
		</ul>
	@endif
</li>
