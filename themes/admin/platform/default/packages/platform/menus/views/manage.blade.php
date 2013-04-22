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
		persistedSlugs : {{ $persistedSlugs }}
	});
});
</script>
@stop

{{-- Page content --}}
@section('content')
<section id="menus">

	<header class="clearfix">
		<h1><a class="icon-reply" href="{{ URL::toAdmin('menus') }}"></a> {{ ! empty($menu) ? 'Update' : 'Create' }} Menu</h1>
		<nav class="tertiary-navigation">
			@widget('platform/ui::nav.show', array(2, 1, 'nav nav-pills', app('platform.admin.uri')))
		</nav>
	</header>

	<hr>

	<section class="content">

		<form id="menu" method="post" action="">

			<div class="actions clearfix">
				<div class="form-inline pull-left">
					<label class="control-label" for="menu-name">Name</label>
					<input type="text" name="menu-name" id="menu-name" class="" value="{{ ! empty($menu) ? $menu->name : '' }}" required>

					<label class="control-label" for="menu-slug">Slug</label>
					<input type="text" name="menu-slug" id="menu-slug" class="" value="{{ ! empty($menu) ? $menu->slug : '' }}" required>
				</div>
				<div class="pull-right">
					<a href="#create-child" role="button" class="btn btn-large" data-toggle="modal">Create</a>
					<button type="submit" class="btn btn-large btn-primary btn-save-menu">@lang('button.update')</button>
				</div>
			</div>

			<hr>

			<div id="create-child" class="modal hide fade">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3>New Children</h3>
				</div>
				<div class="modal-body">
					<fieldset id="menu-new-child">
						<!-- Item Name -->
						<div class="control-group">
							<label for="newitem-name">Name</label>
							<input type="text" name="newitem-name" id="newitem-name" class="input-block-level" value="" placeholder="">
						</div>

						<!-- Item Slug -->
						<div class="control-group">
							<label for="newitem-slug">Slug</label>
							<input type="text" name="newitem-slug" id="newitem-slug" class="input-block-level" value="" placeholder="">
						</div>

						<!-- Target -->
						<div class="control-group">
							<label class="control-label" for="newitem-target">Target</label>
							<div class="controls">
								<select name="newitem-target" id="newitem-target" class="child-target">
									<option value="0">Same Window</option>
									<option value="1">New Window</option>
									<option value="2">Parent Frame</option>
									<option value="3">Top Frame (Main Document)</option>
								</select>
							</div>
						</div>

						<!-- CSS Class -->
						<div class="control-group">
							<label for="newitem-css_class">CSS Class</label>
							<input type="text" name="newitem-css_class" id="newitem-css_class" class="input-block-level" value="" placeholder="">
						</div>
					</fieldset>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-large" data-dismiss="modal" aria-hidden="true">Close</button>
					<button type="button" name="newitem-add" id="newitem-add" class="btn btn-large btn-primary children-add-new" data-dismiss="modal">
						Add Children
					</button>
				</div>
			</div>

			<div class="nestable" id="nestable">
				@include('platform/menus::children', compact('children'))
			</div>

			<hr>

			<div class="actions clearfix">
				<div class="pull-right">
					<a href="#create-child" role="button" class="btn btn-large" data-toggle="modal">Create</a>
					<button type="submit" class="btn btn-large btn-primary btn-save-menu">@lang('button.update')</button>
				</div>
			</div>

		</form>
	</section>
</section>
@stop
