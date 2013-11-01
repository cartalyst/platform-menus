@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{{ trans("platform/menus::general.{$pageSegment}.title") }}} {{{ ! empty($menu) ? '- ' . $menu->name : null }}} ::
@parent
@stop

{{-- Queue assets --}}
{{ Asset::queue('menus', 'platform/menus::css/menus.css', 'styles') }}
{{ Asset::queue('jquery.slugify', 'js/platform/slugify.js', 'jquery') }}
{{ Asset::queue('jquery.sortable', 'platform/menus::js/jquery.sortable.js', 'jquery')}}
{{ Asset::queue('jquery.menu-manager', 'platform/menus::js/jquery.menumanager.js', array('jquery.slugify', 'jquery.sortable')) }}
{{ Asset::queue('underscore', 'js/underscore/underscore.js', 'jquery.menu-manager') }}

{{-- Inline scripts --}}
@section('scripts')
@parent
<script>
$(function() {

	// Instantiate a new Menu Manager
	var MenuManager = $.menumanager('#menu-form');

	// Set the persisted slugs
	MenuManager.setPersistedSlugs({{ json_encode($persistedSlugs) }});

	// Register the available types
	@foreach ($types as $type)
		MenuManager.registerType('{{ $type->getName() }}', '{{ $type->getIdentifier() }}');
	@endforeach

});
</script>
@stop

{{-- Page content --}}
@section('content')

<form id="menu-form" action="{{ Request::fullUrl() }}" method="POST" accept-char="UTF-8">

	{{-- CSRF Token --}}
	<input type="hidden" name="_token" value="{{ csrf_token() }}">

	<div class="row">

		<div class="col-md-12">

			{{-- Page header --}}
			<div class="page-header">

				<div class="pull-right">
					<button class="btn btn-success btn-lg" type="submit"><i class="icon-save"></i> {{{ trans('platform/menus::button.save') }}}</button>
				</div>

				<h1>{{{ trans("platform/menus::general.{$pageSegment}.title") }}} <small>{{{ ! empty($menu) ? $menu->name : null }}}</small></h1>

			</div>

		</div>

	</div>

	<div class="row">

		{{-- Menu Items --}}
		<div class="col-md-7">

			<div data-no-items class="jumbotron{{ ! empty($children) ? ' hide' : null }}">

				<div class="container" id="no-children">

					<h1>{{ trans('platform/menus::message.no_children') }}</h1>

					<p>&nbsp;</p>

					<p><button class="btn btn-primary btn-md" data-item-add><i class="icon-plus"></i> {{{ trans('platform/menus::button.add_item') }}}</button></p>

				</div>

			</div>

			<ol class="items">
				<li class="item-add{{ empty($children) ? ' hide' : null }}" data-item-add>
					<div class="item-name">{{{ trans('platform/menus::button.add_item') }}}</div>
				</li>
			</ol>

			<div id="sortable">
				<ol class="items">
					@if ( ! empty($children))
					@each('platform/menus::children', $children, 'child')
					@endif

					{{-- Underscore children template --}}
					@include('platform/menus::children-template')
				</ol>
			</div>

		</div>

		{{-- Sidebar --}}
		<div class="col-md-5">

			{{-- Root form --}}
			<div class="well well-md item-box-borderless" data-root-form>

				<fieldset>

					<legend>Menu Details</legend>

					<div class="form-group">
						<label class="control-label" for="menu-name">{{ trans('platform/menus::form.name') }}</label>
						<input type="text" class="form-control" name="menu-name" id="menu-name" value="{{{ ! empty($menu) ? $menu->name : null }}}" required>
					</div>

					<div class="form-group">
						<label class="control-label" for="menu-slug">{{ trans('platform/menus::form.slug') }}</label>
						<input type="text" class="form-control" name="menu-slug" id="menu-slug" value="{{{ ! empty($menu) ? $menu->slug : null }}}" required>
					</div>

				</fieldset>

			</div>

			{{-- Items form --}}
			@if ( ! empty($children))
			@each('platform/menus::form', $children, 'child')
			@endif

			{{-- New children form --}}
			@include('platform/menus::form')

			{{-- Underscore form template --}}
			<div data-forms>
				@include('platform/menus::form-template')
			</div>

		</div>

	</div>

</form>

@stop
