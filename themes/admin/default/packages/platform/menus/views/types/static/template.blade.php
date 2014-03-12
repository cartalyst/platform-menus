<div class="form-group<%= type != 'static' ? ' hide' : null %>" data-item-type="static">
	<label class="control-label" for="<%= slug %>_static_uri">{{{ trans('platform/menus::form.uri') }}}</label>

	<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.uri_help') }}}"></i>

	<input data-item-form="<%= slug %>" type="text" name="children[<%= slug %>][static][uri]" id="<%= slug %>_static_uri" class="form-control" value="<%= static_uri %>">
</div>
