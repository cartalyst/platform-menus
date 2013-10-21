@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{{ trans("platform/menus::general.{$pageSegment}.title") }}} {{{ ! empty($menu) ? '- ' . $menu->name : null }}} ::
@parent
@stop

{{-- Queue assets --}}
{{ Asset::queue('menus', 'platform/menus::css/menus.css') }}
{{ Asset::queue('tab', 'js/bootstrap/tab.js', 'jquery') }}
{{ Asset::queue('modal', 'js/bootstrap/modal.js', 'jquery') }}
{{ Asset::queue('tempo', 'js/tempo/tempo.js', 'jquery') }}
{{ Asset::queue('slugify', 'js/platform/slugify.js', 'jquery') }}
{{ Asset::queue('nestable', 'platform/menus::js/jquery.nestable.js', 'jquery')}}
{{ Asset::queue('menumanager', 'platform/menus::js/jquery.menumanager.js', 'nestable') }}

{{-- Inline assets --}}
@section('assets')
@parent
@stop

{{-- Inline scripts --}}
@section('scripts')
@parent
<script>
$(function() {

	$.menumanager('#menu-form', {
		persistedSlugs : {{ json_encode($persistedSlugs) }}
	});

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
					<button class="btn btn-success btn-lg" type="submit"><i class="icon-save"></i> {{ trans('platform/menus::button.save') }}</button>
				</div>

				<h1>{{{ trans("platform/menus::general.{$pageSegment}.title") }}} <small>{{{ ! empty($menu) ? $menu->name : null }}}</small></h1>

			</div>

		</div>

		{{-- Menu Items --}}
		<div class="col-md-7">

			<div class="nestable" id="nestable">
				<ol class="items">
					<li class="item item-add{{ empty($children) ? ' hide' : null }}" data-item-add>
						<div data-item class="item-name">{{ trans('platform/menus::button.add_item') }}</div>
					</li>

					@if ( ! empty($children))
					@each('platform/menus::children', $children, 'child')
					@endif

					{{-- TempoJs Template --}}
					@include('platform/menus::children')
				</ol>
			</div>

			<div data-no-items class="jumbotron{{ ! empty($children) ? ' hide' : null }}">

				<div class="container" id="no-children">

					<h1>{{ trans('platform/menus::message.no_children') }}</h1>

					<p>&nbsp;</p>

					<p><button class="btn btn-primary btn-md" data-item-add><i class="icon-plus"></i> {{ trans('platform/menus::button.add_item') }}</button></p>

				</div>

			</div>

		</div>

		{{-- Sidebar --}}
		<div class="col-md-5">

			{{-- Root form --}}
			<div class="well well-md" id="root-details">

				<fieldset>

					<legend>Menu Details</legend>

					<div class="form-group">
						<label class="control-label" for="menu-name">{{ trans('platform/menus::form.root.name') }}</label>
						<input type="text" class="form-control" name="menu-name" id="menu-name" value="{{{ ! empty($menu) ? $menu->name : null }}}" required>
					</div>

					<div class="form-group">
						<label class="control-label" for="menu-slug">{{ trans('platform/menus::form.root.slug') }}</label>
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

			{{-- TempoJs form --}}
			<div id="forms">
				@include('platform/menus::form-tempojs')
			</div>

		</div>

	</div>

</form>

@stop
