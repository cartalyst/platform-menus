<div class="form-group<%= type != 'static' ? ' hide' : null %>" data-item-type="static">

	<label class="control-label" for="<%= slug %>_static_uri">
	<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.uri_help') }}}"></i>
	{{{ trans('platform/menus::model.uri') }}}
	</label>

	<input class="form-control input-sm" data-item-form="<%= slug %>" type="text" name="children[<%= slug %>][static][uri]" id="<%= slug %>_static_uri" value="<%= static_uri %>">
</div>
