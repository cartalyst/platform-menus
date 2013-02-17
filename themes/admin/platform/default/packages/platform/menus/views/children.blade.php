<li class="dd-item" data-slug="{{ $item->slug }}">
	<div class="dd-handle">{{ $item->name }}</div>
	@if ($children = $item->getChildren())
	<ol class="dd-list">
	@foreach ($children as $child)
		@include('platform/menus::children', array('item' => $child))
	@endforeach
	</ol>
	@endif
</li>
