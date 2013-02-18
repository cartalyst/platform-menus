<li class="child dd-item dd3-item" data-slug="{{ $item->slug }}">
	<div class="dd-handle dd3-handle">Drag</div>
	<div class="dd3-content">
		<div class="remove" style="float: right;">x</div>
		{{ $item->name }}
	</div>
	<!--
	<div class="dd-handlex teste-handle toggle-children">Toogle Details</div>
	<div class="child-details" style="display: none;">
		something here
	</div>
	-->
@if ($children = $item->getChildren())
	<ol class="dd-list">
	@foreach ($children as $child)
		@include('platform/menus::children', array('item' => $child))
	@endforeach
	</ol>
@endif
</li>
