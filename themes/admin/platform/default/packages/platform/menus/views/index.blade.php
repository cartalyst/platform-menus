@extends('templates/default')

{{-- Page title --}}
@section('title')
@lang('platform/menus::general.title') ::
@parent
@stop

{{-- Page content --}}
@section('content')
<section id="menus">

	<header class="clearfix">
		<h1>@lang('platform/menus::general.title')</h1>
		<nav class="tertiary-navigation">
			@widget('platform/ui::nav.show', array(2, 1, 'nav nav-pills', app('platform.admin.uri')))
		</nav>
	</header>

	<hr>

	<section class="content">

		<div class="actions clearfix">
			<a class="btn btn-large btn-primary pull-right" href="{{ URL::toAdmin('menus/create') }}">@lang('button.create')</a>
		</div>

		<hr>

		<table class="table table-bordered">
			<thead>
				<tr>
					<th>@lang('platform/menus::table.name')</th>
					<th>@lang('platform/menus::table.slug')</th>
					<th>@lang('platform/menus::table.children_count')</th>
					<th class="span2">@lang('table.actions')</th>
				</tr>
			</thead>
			<tbody>
			@foreach ($menus as $menu)
				<tr>
					<td>{{ $menu->name }}</td>
					<td>{{ $menu->slug }}</td>
					<td>@lang('platform/menus::table.children', array('count' => $menu->getChildrenCount()))</td>
					<td>
						<a class="btn btn-small" href="{{ URL::toAdmin("menus/edit/{$menu->slug}") }}">@lang('button.edit')</a>
						<a class="btn btn-small btn-danger" href="{{ URL::toAdmin("menus/delete/{$menu->slug}") }}">@lang('button.delete')</a>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>

	</section>

</section>
@stop
