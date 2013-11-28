<script type="text/template" data-grid="main" id="data-grid-tmpl">

	<% _.each(results, function(r) { %>

		<tr>
			<td><%= r.name %></td>
			<td><%= r.slug %></td>
			<td><%= r.items_count %></td>
			<td><%= r.created_at %></td>
			<td>
				<a class="btn btn-primary tip" href="{{ URL::toAdmin('menus/<%= r.slug %>/edit') }}" title="{{{ trans('platform/menus::button.update') }}}"><i class="fa fa-edit"></i></a>

				<a class="btn btn-danger tip" data-toggle="modal" data-target="modal-confirm" href="{{ URL::toAdmin('menus/<%= r.slug %>/delete') }}" title="{{{ trans('platform/menus::button.delete') }}}"><i class="fa fa-trash-o"></i></a>
			</td>
		</tr>

	<% }); %>

</script>
