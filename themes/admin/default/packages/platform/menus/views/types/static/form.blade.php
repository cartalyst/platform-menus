<?php
$childId   = ! empty($child) ? "{$child->id}_%s" : 'new-child_%s';
$childName = ! empty($child) ? "children[{$child->id}]%s" : 'new-child_%s';
?>

<div class="form-group{{ ( ! empty($child) and $child->type != 'static') ? ' hide' : null }}" data-item-type="static">

	<label class="control-label" for="{{ sprintf($childId, 'static_uri') }}">
		<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::mdoel.uri_help') }}}"></i>
		{{{ trans('platform/menus::model.general.uri') }}}
	</label>

	<input class="form-control input-sm" data-item-form="{{{ ! empty($child) ? $child->id : 'new-child' }}}" type="text" name="{{ sprintf($childName, '[static][uri]') }}" id="{{ sprintf($childId, 'static_uri') }}"  value="{{ ! empty($child) ? $child->uri : null }}">

</div>
