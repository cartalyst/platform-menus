<ol class="dd-list">
@foreach ($children as $child)
	<li class="dd-item dd3-item" data-slug="{{ $child->slug }}">
		<div class="dd-handle dd3-handle">Drag</div>

		<div class="child-name">{{ $child->name }}</div>

		<div class="teste-handle toggle-children">Toogle Details</div>

		<div class="child-details">

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
