@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
: {{{ trans("action.{$mode}") }}} {{{ $menu->exists ? '- ' . $menu->name : null }}}
@stop

{{-- Queue assets --}}
{{ Asset::queue('menus', 'platform/menus::css/menus.css', 'styles') }}
{{ Asset::queue('jquery.slugify', 'platform/js/slugify.js', 'jquery') }}
{{ Asset::queue('jquery.sortable', 'platform/menus::js/jquery.sortable.js', 'jquery')}}
{{ Asset::queue('jquery.menu-manager', 'platform/menus::js/jquery.menumanager.js', ['jquery.slugify', 'jquery.sortable']) }}
{{ Asset::queue('underscore', 'underscore/js/underscore.js', 'jquery.menu-manager') }}

{{-- Inline scripts --}}
@section('scripts')
@parent
<script>
	jQuery(document).ready(function($)
	{
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

{{-- Inline styles --}}
@section('styles')
@parent
@stop

{{-- Page content --}}
@section('page')
<section class="panel panel-default panel-tabs">

	<form id="menu-form" action="{{ request()->fullUrl() }}" method="POST" accept-char="UTF-8">

		{{-- CSRF Token --}}
		<input type="hidden" name="_token" value="{{ csrf_token() }}">


		<header class="panel-heading">

			<nav class="navbar navbar-default navbar-actions">

				<div class="container-fluid">

					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#actions">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>

						<ul class="nav navbar-nav navbar-cancel">
							<li>
								<a class="tip" href="{{ route('admin.menus.all') }}" data-toggle="tooltip" data-original-title="{{{ trans('action.cancel') }}}">
									<i class="fa fa-reply"></i>  <span class="visible-xs-inline">{{{ trans('action.cancel') }}}</span>
								</a>
							</li>
						</ul>

						<span class="navbar-brand">{{{ trans("action.{$mode}") }}} <small>{{{ $menu->exists ? $menu->name : null }}}</small></span>
					</div>

					{{-- Form: Actions --}}
					<div class="collapse navbar-collapse" id="actions">

						<ul class="nav navbar-nav navbar-right">

							<li>
								<button class="btn btn-primary navbar-btn" data-toggle="tooltip" data-original-title="{{{ trans('action.save') }}}">
									<i class="fa fa-save"></i>  <span class="visible-xs-inline">{{{ trans('action.save') }}}</span>
								</button>
							</li>

						</ul>

					</div>

				</div>

			</nav>

		</header>

		<main class="panel-body">

			<div class="tab-pane">

					{{-- Menu Items --}}
					<div class="col-md-7">

						<div data-no-items class="jumbotron{{ ! empty($children) ? ' hide' : null }}">

							<div class="container" id="no-children">

								<h1>{{ trans('platform/menus::message.no_children') }}</h1>

								<p>&nbsp;</p>

								<p><button class="btn btn-primary btn-md" data-item-add data-item="new-child"><i class="fa fa-plus"></i> {{{ trans('platform/menus::action.add_item') }}}</button></p>

							</div>

						</div>

						<ol class="items{{ empty($children) ? ' hide' : null }}" data-item-add>
							<li class="item-add">
								<div class="item-name" data-item="new-child">{{{ trans('platform/menus::action.add_item') }}}</div>
							</li>
						</ol>

						<div id="sortable">
							<ol class="items">
								@if ( ! empty($children))
								@each('platform/menus::manage/children', $children, 'child')
								@endif

								{{-- Underscore children template --}}
								@include('platform/menus::manage/children-template')
							</ol>
						</div>

					</div>

					{{-- Sidebar --}}
					<div class="col-md-5">

						{{-- Root form --}}
						<div class="well well-md item-box-borderless" data-root-form>

							<fieldset>

								<legend>Menu Details</legend>

								<div class="form-group{{ Alert::form('name', ' has-error') }}">
									<label class="control-label" for="menu-name">{{ trans('platform/menus::model.name') }}</label>

									<input type="text" class="form-control" name="menu-name" id="menu-name" value="{{{ $menu->exists ? $menu->name : null }}}" required>

									<span class="help-block">{{{ Alert::form('name') }}}</span>
								</div>

								<div class="form-group{{ Alert::form('slug', ' has-error') }}">
									<label class="control-label" for="menu-slug">{{ trans('platform/menus::model.slug') }}</label>

									<input type="text" class="form-control" name="menu-slug" id="menu-slug" value="{{{ $menu->exists ? $menu->slug : null }}}" required>

									<span class="help-block">{{{ Alert::form('slug') }}}</span>
								</div>

							</fieldset>

						</div>

						{{-- Items form --}}
						@if ( ! empty($children))
						@each('platform/menus::manage/form', $children, 'child')
						@endif

						{{-- New children form --}}
						@include('platform/menus::manage/form')

						{{-- Underscore form template --}}
						<div data-forms>
							@include('platform/menus::manage/form-template')
						</div>

					</div>

				</div>

		</main>

	</form>

</section>
@stop
