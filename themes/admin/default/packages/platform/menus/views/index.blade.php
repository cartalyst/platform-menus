@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{{ trans('platform/menus::general.title') }}} ::
@parent
@stop

{{-- Queue assets --}}
{{ Asset::queue('tempo', 'js/tempo/tempo.js', 'jquery') }}
{{ Asset::queue('data-grid', 'js/cartalyst/data-grid.js', 'tempo') }}

{{-- Inline scripts --}}
@section('scripts')
@parent
<script>
$(function()
{

	$.datagrid('main', '.data-grid', '.data-grid_pagination', '.data-grid_applied', {
		loader: '.loading',
		type: 'single',
		sort: {
			column: 'created_at',
			direction: 'desc'
		},
		callback: function(obj) {

			$('.total').html(obj.filterCount);
			$('.tip').tooltip();

		}
	});

});
</script>
@stop

{{-- Page content --}}
@section('content')

<div class="row">

	<div class="col-md-12">

		{{-- Page header --}}
		<div class="page-header">

			<span class="pull-right">

				<form method="post" action="" accept-charset="utf-8" data-search data-grid="main" class="form-inline" role="form">

					<div class="form-group">

						<div class="loading"></div>

					</div>

					<div class="form-group">
						<select class="form-control" name="column">
							<option value="all">{{{ trans('general.all') }}}</option>
							<option value="name">{{{ trans('platform/menus::table.name') }}}</option>
							<option value="slug">{{{ trans('platform/menus::table.slug') }}}</option>
							<option value="created_at">{{{ trans('platform/menus::table.created_at') }}}</option>
						</select>
					</div>

					<div class="form-group">
						<input name="filter" type="text" placeholder="{{{ trans('general.search') }}}" class="form-control">
					</div>

					<button class="btn btn-default"><i class="icon-search"></i></button>
				</form>

			</span>

			<h1>{{{ trans('platform/menus::general.title') }}}</h1>

		</div>

		<div class="row">

			{{-- Data Grid : Applied Filters --}}
			<div class="col-lg-10">

				<div class="data-grid_applied" data-grid="main">

					<span data-template style="display: none;">

						<button type="button" class="btn btn-info tip" title="Remove filter">
							[? if column == undefined ?]
								[[ valueLabel ]]
							[? else ?]
								[[ valueLabel ]] {{{ trans('general.in') }}} <em>[[ columnLabel ]]</em>
							[? endif ?]
							<i class="icon-remove-sign"></i>
						</button>

					</span>

				</div>

			</div>

			<div class="col-lg-2 text-right">
				<a class="btn btn-warning" href="{{ URL::toAdmin('menus/create') }}"><i class="icon-plus"></i> {{{ trans('button.create') }}}</a>
			</div>

		</div>

		<br />

		<table data-source="{{ URL::toAdmin('menus/grid') }}" data-grid="main" class="data-grid table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th data-sort="name" data-grid="main" class="col-md-3 sortable">{{{ trans('platform/menus::table.name') }}}</th>
					<th data-sort="slug" data-grid="main" class="col-md-2 sortable">{{{ trans('platform/menus::table.slug') }}}</th>
					<th data-sort="children_count" data-grid="main" class="col-md-2 sortable">{{{ trans('platform/menus::table.children_count') }}}</th>
					<th data-sort="created_at" data-grid="main" class="col-md-3 sortable">{{{ trans('platform/menus::table.created_at') }}}</th>
					<th class="col-md-2"></th>
				</tr>
			</thead>
			<tbody>
				<tr data-template style="display: none;">
					<td>[[ name ]]</td>
					<td>[[ slug ]]</td>
					<td>[[ children_count ]]</td>
					<td>[[ created_at ]]</td>
					<td>
						<a class="btn btn-primary tip" href="{{ URL::toAdmin('menus/edit/[[ slug ]]') }}" title="{{{ trans('button.edit') }}}"><i class="icon-edit"></i></a>

						<a class="btn btn-danger tip" data-toggle="modal" data-target="modal-confirm" href="{{ URL::toAdmin('menus/delete/[[ slug ]]') }}" title="{{{ trans('button.delete') }}}"><i class="icon-trash"></i></a>
					</td>
				</tr>
				<tr data-results-fallback>
					<td colspan="5" class="no-results">
						{{{ trans('table.no_results') }}}
					</td>
				</tr>
			</tbody>
		</table>

		{{-- Data Grid : Pagination --}}
		<div class="data-grid_pagination" data-grid="main">
			<div data-template style="display: none;">

				<div class="pull-right">

					<ul class="pagination pagination-sm">
						[? if prevPage !== null ?]
						<li><a data-page="[[ prevPage ]]"><i class="icon-chevron-left"></i></a></li>
						[? else ?]
						<li class="disabled"><a><i class="icon-chevron-left"></i></a></li>
						[? endif ?]

						[? if nextPage !== null ?]
						<li><a  data-page="[[ nextPage ]]"><i class="icon-chevron-right"></i></a></li>
						[? else ?]
						<li class="disabled"><a><i class="icon-chevron-right"></i></a></li>
						[? endif ?]
					</ul>

				</div>

				{{{ trans('general.showing') }}} [[ pageStart ]] {{{ trans('general.to') }}} [[ pageLimit ]] {{{ trans('general.of') }}} <span class="total"></span>

			</div>
		</div>

	</div>

</div>

@stop
