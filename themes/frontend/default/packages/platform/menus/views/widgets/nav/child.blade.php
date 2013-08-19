<?php
$children = $child->getChildren();
?>
<li class="{{ $child->in_active_path ? 'active' : null }}{{ $children ? ' dropdown' : null }}">
	<a href="{{ URL::to($child->uri) }}"@if ($children) id="drop-{{ $child->slug }}" role="button" class="dropdown-toggle" data-toggle="dropdown"@endif>
		<i class="{{ $child->class }}"></i>
		<span>{{ $child->name }}</span>
		@if ($children)
		<b class="caret"></b>
		@endif
	</a>

	@if ($children)
		<ul class="dropdown-menu" role="menu"  aria-labelledby="drop-{{ $child->slug }}">
		@each('platform/menus::widgets/nav/child', $children, 'child')
		</ul>
	@endif
</li>
