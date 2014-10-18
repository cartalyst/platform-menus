<script type="text/template" data-grid="main" data-template="results">

	<% _.each(results, function(r) { %>

		<tr>
			<td><input type="checkbox" name="entries[]" value="<%= r.id %>"></td>
			<td><a href="{{ url()->toAdmin('menus/<%= r.id %>') }}"><%= r.name %></a></td>
			<td><%= r.slug %></td>
			<td><%= r.items_count %></td>
			<td><%= moment(r.created_at).format('MMM DD, YYYY') %></td>
		</tr>

	<% }); %>

</script>
