<li class="{{ $child->in_active_path ? 'active' : null }}">
	<a href="{{ URL::to($child->uri) }}">
		<i class="{{ $child->class }}"></i>
		{{ $child->name }}
	</a>

	@if ($child->children)
		@each('platform/menus::widgets/nav/child', $child->children, 'child')
	@endif
</li>
