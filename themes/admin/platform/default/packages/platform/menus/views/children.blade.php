<ol class="items">
@foreach ($children as $child)
	<li class="item" data-slug="{{ $child->slug }}">
		<div class="item-dd-handle">Drag</div>

		<div class="item-toggle">Toogle Details</div>

		<div class="item-name">{{ $child->name }}</div>

		<div class="item-details">

			<input type="text" name="children[{{ $child->slug }}][name]" value="{{ $child->name }}"><br/>
			<input type="text" name="children[{{ $child->slug }}][slug]" value="{{ $child->slug }}">

			<br ><br>
			<button name="remove" class="remove">Delete</button>

		</div>

	@if ($children = $child->getChildren())
		@include('platform/menus::children', compact('children'))
	@endif
	</li>
@endforeach
</ol>
