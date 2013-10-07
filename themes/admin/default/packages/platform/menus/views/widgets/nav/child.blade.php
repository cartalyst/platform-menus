<li class="{{ $child->in_active_path ? 'active' : null }}">
	<a target="{{ $child->target }}" href="{{ URL::to($child->uri) }}">
		@if ( $child->class == '' )
			<i class="icon-circle-blank"></i>
		@else
			<i class="{{ $child->class }}"></i>
		@endif
		<span>{{ $child->name }}</span>
	</a>

	@if ($child->children)
		@each('platform/menus::widgets/nav/child', $child->children, 'child')
	@endif
</li>
