@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
: {{{ trans("action.{$mode}") }}} {{{ $menu->exists ? '- ' . $menu->name : null }}}
@stop

{{-- Queue assets --}}
{{ Asset::queue('menus', 'platform/menus::css/menus.scss', 'style') }}

{{ Asset::queue('slugify', 'platform/js/slugify.js', 'jquery') }}
{{ Asset::queue('sortable', 'platform/menus::js/jquery.sortable.js', 'jquery')}}
{{ Asset::queue('validate', 'platform/js/validate.js', 'jquery') }}
{{ Asset::queue('selectize', 'selectize/js/selectize.js', 'jquery') }}
{{ Asset::queue('menu-manager', 'platform/menus::js/jquery.menumanager.js', 'slugify') }}
{{ Asset::queue('underscore', 'underscore/js/underscore.js', 'menu-manager') }}


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

{{-- Page --}}
@section('page')
<section class="panel panel-default">

	<form id="menu-form" action="{{ request()->fullUrl() }}" method="POST" accept-char="UTF-8" data-parsley-validate>

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

							@if ($menu->exists and $mode != 'copy')
							<li>
								<a href="{{ route('admin.menu.delete', $menu->id) }}" class="tip" data-action-delete data-toggle="tooltip" data-original-title="{{{ trans('action.delete') }}}" type="delete">
									<i class="fa fa-trash-o"></i>  <span class="visible-xs-inline">{{{ trans('action.delete') }}}</span>
								</a>
							</li>
							@endif

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

		<div class="panel-body">

			{{-- Form: General --}}
			<fieldset>

				<legend>{{{ $menu->exists ? $menu->name : null }}} Menu</legend>

				<div class="row">
					<div class="col-sm-6">
						{{-- Name --}}
						<div class="form-group{{ Alert::form('name', ' has-error') }}">

							<label class="control-label" for="menu-name">
								<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.name_help') }}}"></i>
								{{ trans('platform/menus::model.name') }}
							</label>

							<input type="text" class="form-control" name="menu-name" id="menu-name" value="{{{ $menu->exists ? $menu->name : null }}}" placeholder="{{{ trans('platform/menus::model.name') }}}" required data-parsley-trigger="change">

							<span class="help-block"></span>
						</div>
					</div>
					<div class="col-sm-6">
						{{-- Slug --}}
						<div class="form-group{{ Alert::form('slug', ' has-error') }}">
							<label class="control-label" for="menu-slug">
								<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.slug_help') }}}"></i>
								{{ trans('platform/menus::model.slug') }}
							</label>

							<input type="text" class="form-control" name="menu-slug" id="menu-slug" value="{{{ $menu->exists ? $menu->slug : null }}}" placeholder="{{{ trans('platform/menus::model.name') }}}" required data-parsley-trigger="change">

							<span class="help-block"></span>
						</div>
					</div>

				</div>

				{{-- Underscore form template --}}
				@include('platform/menus::manage/form-template')

			</fieldset>

			{{-- Form: Structure --}}
			<fieldset>

				<legend>{{{ $menu->exists ? $menu->name : null }}} Structure</legend>

				@if ( empty($children) )

				<p class="no-items lead text-center">{{ trans('platform/menus::message.no_children') }}</p>

				@endif



				<div id="sortable">

					<ol class="items">
						<li>
						{{-- New children form --}}
						<div class="panel panel-default panel-menu">

							<header class="panel-heading collapsed" data-toggle="collapse" data-target="#panel-new" aria-expanded="false" aria-controls="panel-new">

								<span class="item-name">New Item</span>

								<span class="panel-close small pull-right tip" data-original-title="{{{ trans('action.collapse') }}}"></span>

							</header>

							<div class="panel-body collapse" id="panel-new">

								<div class="row">

									<div class="col-md-12">

										@include('platform/menus::manage/form')

									</div>

								</div>

							</div>

						</div>


					</li>

					{{-- Menu Items --}}
					@if ( ! empty($children))
					@each('platform/menus::manage/children', $children, 'child')
					@endif

				</ol>

			</div>



		</fieldset>

	</div>

</form>

</section>
@stop
