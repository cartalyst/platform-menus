<ol class="items">
@foreach ($children as $child)
	<li class="item" data-slug="{{ $child->slug }}">
		<div class="item-dd-handle">Drag</div>

		<div href="#item-details-{{ $child->slug }}" class="item-toggle" data-toggle="modal">Toggle Details</div>

		<div class="item-name">{{ $child->name }}</div>

		<div id="item-details-{{ $child->slug }}" class="modal hide fade">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>{{ $child->name }} Details</h3>
			</div>

			<div class="modal-body">
				<fieldset id="item-details">
					<input type="text" name="children[{{ $child->slug }}][name]" value="{{ $child->name }}"><br/>
					<input type="text" name="children[{{ $child->slug }}][slug]" value="{{ $child->slug }}">
				</fieldset>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-large" data-dismiss="modal" aria-hidden="true">Close</button>
				<button name="remove" class="remove btn btn-large btn-primary" data-dismiss="modal" aria-hidden="true">Delete</button>
			</div>

		</div>

	@if ($children = $child->getChildren())
		@include('platform/menus::children', compact('children'))
	@endif
	</li>
@endforeach
</ol>
