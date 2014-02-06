<script type="text/template" id="item-template">

<li data-item-id="<%= slug %>">
	<div class="item-handle"><i class="fa fa-bars"></i></div>

	<div class="item-name" data-item="<%= slug %>">
		<span data-item-name="<%= slug %>"><%= name %></span>

		<span class="item-status<%= enabled == 0 ? '' : ' hide' %>" data-item-status="<%= slug %>"><i class="fa fa-eye-slash"></i></span>
	</div>

	<ol>

	</ol>
</li>

</script>
