@extends('templates/default')

{{-- Page title --}}
@section('title')
@lang('platform/menus::general.title') ::
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
		<h1><a class="icon-reply" href="{{ URL::toAdmin('menus') }}"></a> @lang('platform/menus::form.'.(empty($menu) ? 'create' : 'update').'.legend')</h1>
		<nav class="tertiary-navigation">
			@widget('platform/ui::nav.show', array(2, 1, 'nav nav-pills', app('platform.admin.uri')))
		</nav>
	</header>

	<hr>

	<section class="content">

		<form id="menu" method="post" action="">

			<div class="actions clearfix">
				<div class="form-inline pull-left">
					<label class="control-label" for="menu-name">@lang('platform/menus::form.root.name')</label>
					<input type="text" name="menu-name" id="menu-name" class="" value="{{ ! empty($menu) ? $menu->name : '' }}" required>

					<label class="control-label" for="menu-slug">@lang('platform/menus::form.root.slug')</label>
					<input type="text" name="menu-slug" id="menu-slug" class="" value="{{ ! empty($menu) ? $menu->slug : '' }}" required>
				</div>
				<div class="pull-right">
					<a href="#create-child" role="button" class="btn btn-large" data-toggle="modal">@lang('platform/menus::button.add_child')</a>
					<button type="submit" class="btn btn-large btn-primary btn-save-menu">@lang('button.update')</button>
				</div>
			</div>

			<hr>

			<div id="create-child" class="modal hide fade">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3>@lang('platform/menus::form.child.create.legend')</h3>
				</div>
				<div class="modal-body">
					<fieldset id="menu-new-child">

						{{-- Item Name --}}
						<div class="control-group">
							<label class="control-label" for="new-child-name">@lang('platform/menus::form.child.name')</label>
							<input type="text" name="new-child-name" id="new-child-name" class="input-block-level" value="" placeholder="">
						</div>

						{{-- Item Slug --}}
						<div class="control-group">
							<label class="control-label" for="new-child-slug">@lang('platform/menus::form.child.slug')</label>
							<input type="text" name="new-child-slug" id="new-child-slug" class="input-block-level" value="" placeholder="">
						</div>

						{{-- Item Uri --}}
						<div class="control-group">
							<label class="control-label" for="new-child-uri">@lang('platform/menus::form.child.uri')</label>
							<input type="text" name="new-child-uri" id="new-child-uri" class="input-block-level" value="" placeholder="">
						</div>

						{{-- Target --}}
						<div class="control-group">
							<label class="control-label" for="new-child-target">@lang('platform/menus::form.child.target.title')</label>
							<div class="controls">
								<select name="new-child-target" id="new-child-target" class="input-block-level">
									<option value="self">@lang('platform/menus::form.child.target.self')</option>
									<option value="new_window">@lang('platform/menus::form.child.target.blank')</option>
									<option value="parent_frame">@lang('platform/menus::form.child.target.parent')</option>
									<option value="top_frame">@lang('platform/menus::form.child.target.top')</option>
								</select>
							</div>
						</div>

						{{-- CSS Class --}}
						<div class="control-group">
							<label for="new-child-css_class">@lang('platform/menus::form.child.class')</label>
							<input type="text" name="new-child-css_class" id="new-child-css_class" class="input-block-level" value="" placeholder="">
						</div>

					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-medium" data-dismiss="modal" aria-hidden="true">@lang('button.close')</button>
					<button type="button" name="new-child-add" id="new-child-add" class="btn btn-medium btn-primary children-add-new" data-dismiss="modal">@lang('platform/menus::button.add_child')</button>
				</div>
			</div>

			<div class="nestable" id="nestable">
				<ol class="items">
					@each('platform/menus::children', $children, 'child')
				</ol>
			</div>

			<p id="no-children" class="{{ (count($children) > 0) ? 'hide' : '' }}">
				No Children
			</p>

			<hr>

			<div class="actions clearfix">
				<div class="pull-right">
					<a href="#create-child" role="button" class="btn btn-large" data-toggle="modal">@lang('platform/menus::button.add_child')</a>
					<button type="submit" class="btn btn-large btn-primary btn-save-menu">@lang('button.update')</button>
				</div>
			</div>

		</form>
	</section>
</section>
@stop
