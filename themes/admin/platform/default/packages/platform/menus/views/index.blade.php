@extends('templates/default')

<!-- Page titlte -->
@section('title')
{{ Lang::get('platform/menus::general.title') }}
@stop

<!-- Page content -->
@section('content')

<section id="menus">

	<header class="clearfix">
		<h1>{{ Lang::get('platform/menus::general.title') }}</h1>
		<nav class="tertiary-navigation">
			@widget('platform/ui::nav.show', array(2, 1, 'nav nav-pills', app('platform.admin.uri')))
		</nav>
	</header>

	<hr>

	<section class="content">

		<div class="actions clearfix">
			<a class="btn btn-large btn-primary pull-right" href="{{ URL::toAdmin('menus/create') }}">{{ Lang::get('button.create') }}</a>
		</div>

		<table class="table table-bordered">
			<thead>
				<tr>
					<th>{{ Lang::get('platform/menus::table.name') }}</th>
					<th>{{ Lang::get('platform/menus::table.slug') }}</th>
					<th>{{ Lang::get('platform/menus::table.children_count') }}</th>
					<th class="span2">{{ Lang::get('table.actions') }}</th>
				</tr>
			</thead>
			<tbody>
			@foreach ($menus as $menu)
				<tr>
					<td>{{ $menu->name }}</td>
					<td>{{ $menu->slug }}</td>
					<td>{{ Lang::get('platform/menus::table.children', array('count' => $menu->getChildrenCount())) }}</td>
					<td>
						<a class="btn btn-small" href="{{ URL::toAdmin('menus/edit/'.$menu->slug) }}">{{ Lang::get('button.edit') }}</a>
						<a class="btn btn-small btn-danger" href="{{ URL::toAdmin('menus/delete/'.$menu->slug) }}">{{ Lang::get('button.delete') }}</a>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>

	</section>
</section>

@stop
