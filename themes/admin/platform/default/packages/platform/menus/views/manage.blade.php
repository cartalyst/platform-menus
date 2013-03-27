@extends('templates/default')

{{-- Page title --}}
@section('title')
@parent
:: {{ Lang::get('platform/menus::general.title') }}
@stop

{{-- Partial Assets --}}
@section('assets')

{{-- Queue Assets --}}
{{ Asset::queue('menus', 'platform/menus::css/menus.css') }}

{{ Asset::queue('tab', 'js/vendor/bootstrap/tab.js', 'jquery') }}
{{ Asset::queue('tempo', 'js/vendor/tempo/tempo.js', 'jquery') }}
{{ Asset::queue('nestable', 'platform/menus::js/jquery.nestable.js', 'jquery')}}
{{ Asset::queue('menumanager', 'platform/menus::js/jquery.menumanager.js', 'nestable') }}

@stop

{{-- Inline Styles --}}
@section('styles')
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



	$('#nestable-menu').on('click', function(e)
	{
		var target = $(e.target),
			action = target.data('action');
		if (action === 'expand-all') {
			$('.dd').nestable('expandAll');
		}
		if (action === 'collapse-all') {
			$('.dd').nestable('collapseAll');
		}
	});
});
</script>
@stop

{{-- Page content --}}
@section('content')
<form id="menu" method="post" action="">
	<div class="cf" style="margin-bottom: 20px; padding: 10px; background-color: rgba(0, 0, 0, 0.1);">
		<h4>Menu Properties</h4>
		Name: <input type="text" name="menu-name" id="menu-name" value="{{ $menu->name }}" /><br />
		Slug: <input type="text" name="menu-slug" id="menu-slug" value="{{ $menu->slug }}" />
	</div>

	<div style="width: 30%; float: left; padding: 10px; background-color: rgba(0, 0, 0, 0.1);">
		<h4>Add children</h4>

		Name: <input name="newitem-name" id="newitem-name" value="" /><br />
		Slug: <input name="newitem-slug" id="newitem-slug" value="" />

		<p>
			<button name="newitem-add" id="newitem-add">Add Item</button>
		</p>
	</div>

	<div style="width: 60%; margin: 0 0 0 20px; float: left;">

		<!--
		<menu id="nestable-menu" style="padding: 0; padding: 10px; margin: 0 0 20px 0; background-color: rgba(0, 0, 0, 0.1);">
			<button type="button" data-action="expand-all">Expand All</button>
			<button type="button" data-action="collapse-all">Collapse All</button>
		</menu>
		-->

		<div class="dd" id="nestable">
			<ol class="dd-list">
			@foreach ($children as $child)
				@include('platform/menus::children', array('item' => $child))
			@endforeach
			</ol>
		</div>

		<div class="cf"></div>
		<br><br>

		<input type="submit" value="Update the Menu">
	</div>



</form>
@stop
