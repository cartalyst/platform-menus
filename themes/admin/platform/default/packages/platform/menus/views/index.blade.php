@extends('templates/default')

<!-- Page titlte -->
@section('title')
{{ Lang::get('platform/menus::general.title') }}
@stop

<!-- Page content -->
@section('content')
<div class="page-header">
	<h3>
		{{ Lang::get('platform/menus::general.title') }}

		<div class="pull-right">
			<a href="{{ URL::to(ADMIN_URI . '/menus/create') }}" class="btn btn-info btn-small">{{ Lang::get('button.create') }}</a>
		</div>
	</h3>
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
	@foreach($menus as $menu)
		<tr>
			<td>{{ $menu->name }}</td>
			<td>{{ $menu->slug }}</td>
			<td>{{ Lang::get('platform/menus::table.children', array('count' => $menu->getChildrenCount())) }}</td>
			<td>
				<a class="btn btn-small" href="{{ URL::to(ADMIN_URI.'/menus/edit/'.$menu->slug) }}">{{ Lang::get('button.edit') }}</a>
				<a class="btn btn-small btn-danger" href="{{ URL::to(ADMIN_URI.'/menus/delete/'.$menu->slug) }}">{{ Lang::get('button.delete') }}</a>
			</td>
		</tr>
	@endforeach
	</tbody>
</table>
@stop
