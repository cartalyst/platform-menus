<option value="{{ $child->id }}"{{ $child->isCurrent ? ' selected="selected"' : null }}>{{ str_repeat('&nbsp;&nbsp;', $child->depth - 1).$child->name }}</option>

@if ($child->children)
	@each('platform/menus::widgets/dropdown/child', $child->children, 'child')
@endif
