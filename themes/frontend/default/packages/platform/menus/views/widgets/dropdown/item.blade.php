<option value="{{ $item->id }}">{{ str_repeat('&nbsp;&nbsp;', $item->depth - 1).$item->name }}</option>

@if ($item->children)
	@each('platform/menus::widgets/dropdown/item', $item->children, 'item')
@endif
