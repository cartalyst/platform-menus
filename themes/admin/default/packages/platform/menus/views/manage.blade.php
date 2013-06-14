@extends('templates/default')

{{-- Page title --}}
@section('title')
{{ trans("platform/menus::general.{$pageSegment}.title") }} ::
@parent
@stop

{{-- Queue Assets --}}
{{ Asset::queue('menus', 'platform/menus::css/menus.css') }}
{{ Asset::queue('tab', 'js/vendor/bootstrap/tab.js', 'jquery') }}
{{ Asset::queue('modal', 'js/vendor/bootstrap/modal.js', 'jquery') }}
{{ Asset::queue('tempo', 'js/vendor/tempo/tempo.js', 'jquery') }}
{{ Asset::queue('nestable', 'platform/menus::js/jquery.nestable.js', 'jquery')}}
{{ Asset::queue('menumanager', 'platform/menus::js/jquery.menumanager.js', 'nestable') }}

{{-- Partial Assets --}}
@section('assets')
@parent
@stop

{{-- Inline Scripts --}}
@section('scripts')
@parent
<script>
jQuery(document).ready(function($) {
	$('#menu').MenuManager({
		persistedSlugs : {{ json_encode($persistedSlugs) }}
	});
});
</script>
@stop

{{-- Page content --}}
@section('content')
<section id="menus">

	<header class="clearfix">
		<h1><a class="icon-reply" href="{{ URL::toAdmin('menus') }}"></a> {{ trans("platform/menus::form.{$pageSegment}.legend") }}</h1>

		<nav class="tertiary-navigation">
			@widget('platform/menus::nav.show', array(2, 1, 'nav nav-pills', admin_uri()))
		</nav>
	</header>

	<hr>

	<section class="content">

		<form id="menu" method="post" action="">

			<div class="actions clearfix">
				<div class="form-inline pull-left">
					<label class="control-label" for="menu-name">{{ trans('platform/menus::form.root.name') }}</label>
					<input type="text" name="menu-name" id="menu-name" class="" value="{{ ! empty($menu) ? $menu->name : null }}" required>

					<label class="control-label" for="menu-slug">{{ trans('platform/menus::form.root.slug') }}</label>
					<input type="text" name="menu-slug" id="menu-slug" class="" value="{{ ! empty($menu) ? $menu->slug : null }}" required>
				</div>
				<div class="pull-right">
					<a href="#create-child" role="button" class="btn btn-large" data-toggle="modal">{{ trans('platform/menus::button.add_child') }}</a>
					<button type="submit" class="btn btn-large btn-primary btn-save-menu">{{ trans('button.update') }}</button>
				</div>
			</div>

			<hr>

			<div id="create-child" class="modal hide fade">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3>{{ trans('platform/menus::form.child.create.legend') }}</h3>
				</div>
				<div class="modal-body">
					<fieldset id="menu-new-child">
					@include('platform/menus::children-form')
					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-medium" data-dismiss="modal" aria-hidden="true">{{ trans('button.close') }}</button>
					<button type="button" name="new-child_add" id="new-child_add" class="btn btn-medium btn-primary children-add-new" data-dismiss="modal">{{ trans('platform/menus::button.add_child') }}</button>
				</div>
			</div>

			<div class="nestable" id="nestable">
				<ol class="items">
					@if ( ! empty($children))
					@each('platform/menus::children', $children, 'child')
					@endif

					<li data-template class="item" data-slug="[[ slug ]]" style="display: none;">
						<div class="item-dd-handle">{{ trans('platform/menus::button.drag_children') }}</div>

						<div href="#item-details-[[ slug ]]" class="item-toggle" data-toggle="modal">{{ trans('platform/menus::button.toggle_child_details') }}</div>

						<div class="item-name">[[ name ]]</div>

						<div id="item-details-[[ slug ]]" class="modal hide fade">

							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h3>{{ trans('platform/menus::form.child.update.legend', array('menu' => '[[ name ]]')) }}</h3>
							</div>

							<div class="modal-body">
								<fieldset id="item-details">

									<input type="hidden" name="children[[[ slug ]]][id]" value="[[ slug ]]">

									{{-- Name --}}
									<div class="control-group">
										<label class="control-label" for="[[ slug ]]_name">{{ trans('platform/menus::form.child.name') }}</label>
										<input type="text" data-children="[[ slug ]]" name="children[[[ slug ]]][name]" id="[[ slug ]]_name" class="input-block-level" value="[[ name ]]">
									</div>

									{{-- Slug --}}
									<div class="control-group">
										<label class="control-label" for="[[ slug ]]_slug">{{ trans('platform/menus::form.child.slug') }}</label>
										<input type="text" data-children="[[ slug ]]" name="children[[[ slug ]]][slug]" id="[[ slug ]]_slug" class="input-block-level" value="[[ slug ]]">
									</div>

									{{-- Item Type --}}
									<div class="control-group">
										<label class="control-label" for="[[ slug ]]_type">{{ trans('platform/menus::form.child.type.title') }}</label>
										<div class="controls">
											<select data-children="[[ slug ]]" name="children[[[ slug ]]][type]" id="[[ slug ]]_type" class="input-block-level">
												[? if type == 'static' ?]
												<option value="static"  selected="selected">{{ trans('platform/menus::form.child.type.static') }}</option>
												[? else ?]
												<option value="static">{{ trans('platform/menus::form.child.type.static') }}</option>
												[? endif ?]

												[? if type == 'page' ?]
												<option value="page" selected="selected">{{ trans('platform/menus::form.child.type.page') }}</option>
												[? else ?]
												<option value="page">{{ trans('platform/menus::form.child.type.page') }}</option>
												[? endif ?]
											</select>
										</div>
									</div>

									{{-- Item Uri --}}
									<div class="control-group">
										<label class="control-label" for="[[ slug ]]_uri">{{ trans('platform/menus::form.child.uri') }}</label>
										<input type="text" data-children="[[ slug ]]" name="children[[[ slug ]]][uri]" id="[[ slug ]]_uri" class="input-block-level" value="[[ uri ]]">
									</div>

									{{-- Visibility --}}
									<div class="control-group">
										<label class="control-label" for="[[ slug ]]_visibility">{{ trans('platform/menus::form.child.visibility.title') }}</label>
										<div class="controls">
											<select data-children="[[ slug ]]" name="children[[[ slug ]]][visibility]" id="[[ slug ]]_visibility" class="input-block-level">
												[? if visibility == 'always' ?]
												<option value="always" selected="selected">{{ trans('platform/menus::form.child.visibility.always') }}</option>
												[? else ?]
												<option value="always">{{ trans('platform/menus::form.child.visibility.always') }}</option>
												[? endif ?]

												[? if visibility == 'logged_in' ?]
												<option value="logged_in" selected="selected">{{ trans('platform/menus::form.child.visibility.logged_in') }}</option>
												[? else ?]
												<option value="logged_in">{{ trans('platform/menus::form.child.visibility.logged_in') }}</option>
												[? endif ?]

												[? if visibility == 'logged_out' ?]
												<option value="logged_out" selected="selected">{{ trans('platform/menus::form.child.visibility.logged_out') }}</option>
												[? else ?]
												<option value="logged_out">{{ trans('platform/menus::form.child.visibility.logged_out') }}</option>
												[? endif ?]

												[? if visibility == 'admin' ?]
												<option value="admin" selected="selected">{{ trans('platform/menus::form.child.visibility.admin') }}</option>
												[? else ?]
												<option value="admin">{{ trans('platform/menus::form.child.visibility.admin') }}</option>
												[? endif ?]
											</select>
										</div>
									</div>

									{{-- Secure --}}
									<div class="control-group">
										<label class="control-label" for="[[ slug ]]_secure">{{ trans('platform/menus::form.child.secure') }}</label>
										<div class="controls">
											<select data-children="[[ slug ]]" name="children[[[ slug ]]][secure]" id="[[ slug ]]_secure" class="input-block-level">
												[? if secure == '1' ?]
												<option value="1" selected="selected">{{ trans('general.yes') }}</option>
												[? else ?]
												<option value="1">{{ trans('general.yes') }}</option>
												[? endif ?]

												[? if secure == '0' ?]
												<option value="0" selected="selected">{{ trans('general.no') }}</option>
												[? else ?]
												<option value="0">{{ trans('general.no') }}</option>
												[? endif ?]
											</select>
										</div>
									</div>

									{{-- Target --}}
									<div class="control-group">
										<label class="control-label" for="[[ slug ]]_target">{{ trans('platform/menus::form.child.target.title') }}</label>
										<div class="controls">
											<select data-children="[[ slug ]]" name="children[[[ slug ]]][target]" id="[[ slug ]]_target" class="input-block-level">
												[? if target == 'self' ?]
												<option value="self" selected="selected">{{ trans('platform/menus::form.child.target.self') }}</option>
												[? else ?]
												<option value="self">{{ trans('platform/menus::form.child.target.self') }}</option>
												[? endif ?]

												[? if target == 'new_children' ?]
												<option value="new_children" selected="selected">{{ trans('platform/menus::form.child.target.blank') }}</option>
												[? else ?]
												<option value="new_children">{{ trans('platform/menus::form.child.target.blank') }}</option>
												[? endif ?]

												[? if target == 'parent_frame' ?]
												<option value="parent_frame" selected="selected">{{ trans('platform/menus::form.child.target.parent') }}</option>
												[? else ?]
												<option value="parent_frame">{{ trans('platform/menus::form.child.target.parent') }}</option>
												[? endif ?]

												[? if target == 'top_frame' ?]
												<option value="top_frame" selected="selected">{{ trans('platform/menus::form.child.target.top') }}</option>
												[? else ?]
												<option value="top_frame">{{ trans('platform/menus::form.child.target.top') }}</option>
												[? endif ?]
											</select>
										</div>
									</div>

									{{-- CSS Class --}}
									<div class="control-group">
										<label class="control-label" for="[[ slug ]]_class">{{ trans('platform/menus::form.child.class') }}</label>
										<input type="text" data-children="[[ slug ]]" name="children[[[ slug ]]][class]" id="[[ slug ]]_class" class="input-block-level" value="[[ class ]]">
									</div>

									{{-- Enabled --}}
									<div class="control-group">
										<label class="control-label" for="[[ slug ]]_enabled">{{ trans('platform/menus::form.child.enabled') }}</label>
										<div class="controls">
											<select data-children="[[ slug ]]" name="children[[[ slug ]]][enabled]" id="[[ slug ]]_enabled" class="input-block-level">
												[? if enabled == '1' ?]
												<option value="1" selected="selected">{{ trans('general.enabled') }}</option>
												[? else ?]
												<option value="1">{{ trans('general.enabled') }}</option>
												[? endif ?]

												[? if enabled == '0' ?]
												<option value="0" selected="selected">{{ trans('general.disabled') }}</option>
												[? else ?]
												<option value="0">{{ trans('general.disabled') }}</option>
												[? endif ?]
											</select>
										</div>
									</div>
								</fieldset>
							</div>

							<div class="modal-footer">
								<button name="remove" class="remove btn btn-mini btn-primary" data-dismiss="modal" aria-hidden="true">{{ trans('platform/menus::button.remove_child') }}</button>
								<button type="button" class="btn btn-medium" data-dismiss="modal" aria-hidden="true">{{ trans('platform/menus::button.update_child') }}</button>
							</div>

						</div>
					</li>

				</ol>
			</div>

			<p id="no-children"{{ ! empty($children) ? ' class="hide"' : null }}>
				{{ trans('platform/menus::message.no_children') }}
			</p>

			<hr>

			<div class="actions clearfix">
				<div class="pull-right">
					<a href="#create-child" role="button" class="btn btn-large" data-toggle="modal">{{ trans('platform/menus::button.add_child') }}</a>
					<button type="submit" class="btn btn-large btn-primary btn-save-menu">{{ trans('button.update') }}</button>
				</div>
			</div>

		</form>

	</section>

</section>
@stop
