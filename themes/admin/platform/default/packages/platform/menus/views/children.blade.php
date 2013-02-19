<li class="dd-item dd3-item" data-slug="{{ $item->slug }}">
	<div class="dd-handle dd3-handle">Drag</div>

	<div class="dd3-content">{{ $item->name }}</div>

	<div class="child">
		<div class="dd-handlex teste-handle toggle-children">Toogle Details</div>
		<div class="child-details">

			<input type="text" name="children[{{ $item->slug }}][name]" value="{{ $item->name }}"><br/>
			<input type="text" name="children[{{ $item->slug }}][slug]" value="{{ $item->slug }}">

			<br ><br>
			<button name="remove" class="remove">Delete</button>

		</div>
	</div>

@if ($children = $item->getChildren())
	<ol class="dd-list">
	@foreach ($children as $child)
		@include('platform/menus::children', array('item' => $child))
	@endforeach
	</ol>
@endif
</li>
