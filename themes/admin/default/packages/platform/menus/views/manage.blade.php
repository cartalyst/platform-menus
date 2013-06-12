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
												<option value="static" [? if type == 'static' ?] selected="selected"[? endif ?]>{{ trans('platform/menus::form.child.type.static') }}</option>
												<option value="page" [? if type == 'page' ?] selected="selected"[? endif ?]>{{ trans('platform/menus::form.child.type.page') }}</option>
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
												<option value="always"[? if visibility == 'always' ?] selected="selected"[? endif ?]>{{ trans('platform/menus::form.child.visibility.always') }}</option>
												<option value="logged_in"[? if visibility == 'logged_in' ?] selected="selected"[? endif ?]>{{ trans('platform/menus::form.child.visibility.logged_in') }}</option>
												<option value="logged_out"[? if visibility == 'logged_out' ?] selected="selected"[? endif ?]>{{ trans('platform/menus::form.child.visibility.logged_out') }}</option>
												<option value="admin"[? if visibility == 'admin' ?] selected="selected"[? endif ?]>{{ trans('platform/menus::form.child.visibility.admin') }}</option>
											</select>
										</div>
									</div>

									{{-- Secure --}}
									<div class="control-group">
										<label class="control-label" for="[[ slug ]]_secure">{{ trans('platform/menus::form.child.secure') }}</label>
										<div class="controls">
											<select data-children="[[ slug ]]" name="children[[[ slug ]]][secure]" id="[[ slug ]]_secure" class="input-block-level">
												<option value="1"[? if secure == '1' ?] selected="selected"[? endif ?]>{{ trans('general.yes') }}</option>
												<option value="0"[? if secure == '0' ?] selected="selected"[? endif ?]>{{ trans('general.no') }}</option>
											</select>
										</div>
									</div>

									{{-- Target --}}
									<div class="control-group">
										<label class="control-label" for="[[ slug ]]_target">{{ trans('platform/menus::form.child.target.title') }}</label>
										<div class="controls">
											<select data-children="[[ slug ]]" name="children[[[ slug ]]][target]" id="[[ slug ]]_target" class="input-block-level">
												<option value="self"[? if target == 'self' ?] selected="selected" [? endif ?]>{{ trans('platform/menus::form.child.target.self') }}</option>
												<option value="new_children"[? if target == 'new_children' ?] selected="selected"[? endif ?]>{{ trans('platform/menus::form.child.target.blank') }}</option>
												<option value="parent_frame"[? if target == 'parent_frame' ?] selected="selected"[? endif ?]>{{ trans('platform/menus::form.child.target.parent') }}</option>
												<option value="top_frame"[? if target == 'top_frame' ?] selected="selected"[? endif ?]>{{ trans('platform/menus::form.child.target.top') }}</option>
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
												<option value="1"[? if enabled == '1' ?] selected="selected"[? endif ?]>{{ trans('general.enabled') }}</option>
												<option value="0"[? if enabled == '0' ?] selected="selected"[? endif ?]>{{ trans('general.disabled') }}</option>
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
