@extends('templates/default')

{{-- Page title --}}
@section('title', trans("platform/menus::general.{$pageSegment}.title", array('menu' => ! empty($menu) ? $menu->name : null)))

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
$(function() {

	$('#menu-form').MenuManager({
		persistedSlugs : {{ json_encode($persistedSlugs) }}
	});

});
</script>
@stop

{{-- Page content --}}
@section('page')
<form id="menu-form" class="form-inline" action="{{ Request::fullUrl() }}" method="POST" accept-char="UTF-8">

	{{-- CSRF Token --}}
	<input type="hidden" name="_token" value="{{ csrf_token() }}">

	<header class="page__header">

		<nav class="page__navigation">
			@widget('platform/menus::nav.show', array(2, 1, 'nav nav-pills', admin_uri()))
		</nav>

		<div class="page__title">
			<h1>
				<a class="icon-reply" href="{{ URL::toAdmin('menus') }}"></a> {{ trans("platform/menus::general.{$pageSegment}.title", array('menu' => ! empty($menu) ? $menu->name : null)) }}
			</h1>
		</div>

	</header>

	<section class="page__content">

		{{-- Root form --}}
		<div class="clearfix">
			<div class="form-inline">
				<label class="control-label" for="menu-name">{{ trans('platform/menus::form.root.name') }}</label>
				<input type="text" name="menu-name" id="menu-name" class="" value="{{{ ! empty($menu) ? $menu->name : null }}}" required>

				<label class="control-label" for="menu-slug">{{ trans('platform/menus::form.root.slug') }}</label>
				<input type="text" name="menu-slug" id="menu-slug" class="" value="{{{ ! empty($menu) ? $menu->slug : null }}}" required>
			</div>
		</div>

		{{-- Children create modal --}}
		<div id="create-child" class="modal hide fade">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>

				<h3>{{ trans('platform/menus::form.child.create.legend') }}</h3>
			</div>
			<div class="modal-body">
				<fieldset id="menu-new-child">
					@include('platform/menus::children-form')
				</fieldset>
			</div>
			<div class="modal-footer">
				<a class="btn btn-mini btn-inverse" data-dismiss="modal">&times; {{ trans('button.close') }}</a>

				<a class="btn btn-mini btn-success children-add-new" name="new-child_add" id="new-child_add">{{ trans('platform/menus::button.add_child') }}</a>
			</div>
		</div>

		{{-- Children --}}
		<div class="nestable" id="nestable">
			<ol class="items">
				@if ( ! empty($children))
				@each('platform/menus::children', $children, 'child')
				@endif

				@include('platform/menus::children-form-tempojs')
			</ol>
		</div>

		<p id="no-children"{{ ! empty($children) ? ' class="hide"' : null }}>
			{{ trans('platform/menus::message.no_children') }}
		</p>

	</section>
@stop

@section('page__footer')

		<nav class="actions actions--right">
			<ul class="navigation navigation--inline-circle">
				<li>
					<a class="tip" data-placement="top" href="#create-child" data-toggle="modal" title="{{ trans('platform/menus::button.add_child') }}"><i class="icon-plus"></i></a>
				</li>
				<li>
					<button class="tip" data-placement="top" title="{{ trans('button.save') }}" type="submit"><i class="icon-save"></i></button>
				</li>
			</ul>
		</nav>

</form>
@stop
