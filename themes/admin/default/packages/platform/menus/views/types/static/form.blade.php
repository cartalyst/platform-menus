<?php
	$childId   = ! empty($child) ? "{$child->id}_%s" : 'new-child_%s';
	$childName = ! empty($child) ? "children[{$child->id}][%s]" : 'new-child_%s';
?>

<div class="form-group" data-item-type="static">
	<label class="control-label" for="{{ sprintf($childId, 'static_uri') }}">{{{ trans('platform/menus::form.uri') }}}</label>

	<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::form.uri_help') }}}"></i>

	<input data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" type="text" name="{{ sprintf($childName, 'static_uri') }}" id="{{ sprintf($childId, 'uri') }}" class="form-control" value="{{ ! empty($child) ? $child->uri : null }}">
</div>
