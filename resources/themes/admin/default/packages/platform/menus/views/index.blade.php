@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
 {{{ trans('platform/menus::common.title') }}}
@stop

{{-- Queue assets --}}
{{ Asset::queue('bootstrap-daterange', 'bootstrap/css/daterangepicker-bs3.css', 'style') }}

{{ Asset::queue('moment', 'moment/js/moment.js', 'jquery') }}
{{ Asset::queue('lodash', 'cartalyst/js/lodash.min.js') }}
{{ Asset::queue('exojs', 'cartalyst/js/exoskeleton.min.js', 'lodash') }}
{{ Asset::queue('data-grid', 'cartalyst/js/data-grid.js', 'jquery') }}
{{ Asset::queue('index', 'platform/menus::js/index.js', 'platform') }}
{{ Asset::queue('bootstrap-daterange', 'bootstrap/js/daterangepicker.js', 'jquery') }}

{{-- Inline styles --}}
@section('styles')
@parent
@stop

{{-- Inline scripts --}}
@section('scripts')
@parent
@stop

{{-- Page content --}}
@section('page')

{{-- Grid --}}
<section class="panel panel-default panel-grid" data-grid="main">

	{{-- Grid: Header --}}
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

					<span class="navbar-brand">{{{ trans('platform/menus::common.title') }}}</span>

				</div>

				{{-- Grid: Actions --}}
				<div class="collapse navbar-collapse" id="actions">

					<ul class="nav navbar-nav navbar-left">

						<li class="disabled">
							<a data-grid-bulk-action="disable" data-toggle="tooltip" data-original-title="{{{ trans('action.bulk.disable') }}}">
								<i class="fa fa-eye-slash"></i> <span class="visible-xs-inline">{{{ trans('action.bulk.disable') }}}</span>
							</a>
						</li>

						<li class="disabled">
							<a data-grid-bulk-action="enable" data-toggle="tooltip" data-original-title="{{{ trans('action.bulk.enable') }}}">
								<i class="fa fa-eye"></i> <span class="visible-xs-inline">{{{ trans('action.bulk.enable') }}}</span>
							</a>
						</li>

						<li class="danger disabled">
							<a data-grid-bulk-action="delete" data-toggle="tooltip" data-target="modal-confirm" data-original-title="{{{ trans('action.bulk.delete') }}}">
								<i class="fa fa-trash-o"></i> <span class="visible-xs-inline">{{{ trans('action.bulk.delete') }}}</span>
							</a>
						</li>

						<li class="dropdown disabled">
							<a href="#" data-grid-exporter class="dropdown-toggle tip" data-toggle="dropdown" role="button" aria-expanded="false" data-original-title="{{{ trans('action.export') }}}">
								<i class="fa fa-download"></i> <span class="visible-xs-inline">{{{ trans('action.export') }}}</span>
							</a>
							<ul class="dropdown-menu" role="menu">
								<li><a data-grid-download="pdf"><i class="fa fa-file-pdf-o"></i> PDF</a></li>
								<li><a data-grid-download="csv"><i class="fa fa-file-excel-o"></i> CSV</a></li>
								<li><a data-grid-download="json"><i class="fa fa-file-code-o"></i> JSON</a></li>
							</ul>
						</li>

						<li class="primary">
							<a href="{{ route('admin.menu.create') }}" data-toggle="tooltip" data-original-title="{{{ trans('action.create') }}}">
								<i class="fa fa-plus"></i>  <span class="visible-xs-inline">{{{ trans('action.create') }}}</span>
							</a>
						</li>

					</ul>

					{{-- Grid: Filters --}}
					<form class="navbar-form navbar-right" method="post" accept-charset="utf-8" data-grid-search role="form">

						<div class="input-group">

							<span class="input-group-btn">

								<button class="btn btn-default" type="button" disabled>
									{{{ trans('common.filters') }}}
								</button>

								<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>

								<ul class="dropdown-menu" role="menu" data-grid-group="status" data-grid-reset-group>

									<li>
										<a data-grid-filter="enabled" data-grid-query="enabled:1" data-grid-label="{{{ trans('common.all_enabled') }}}" data-grid-reset>
											<i class="fa fa-eye"></i> {{{ trans('common.show_enabled') }}}
										</a>
									</li>

									<li>
										<a data-grid-filter="disabled" data-grid-query="enabled:0" data-grid-label="{{{ trans('common.all_disabled') }}}" data-grid-reset>
											<i class="fa fa-eye-slash"></i> {{{ trans('common.show_disabled') }}}
										</a>
									</li>

									<li class="divider"></li>

									<li>
										<a data-grid-calendar-preset="day">
											<i class="fa fa-calendar"></i> {{{ trans('date.day') }}}
										</a>
									</li>

									<li>
										<a data-grid-calendar-preset="week">
											<i class="fa fa-calendar"></i> {{{ trans('date.week') }}}
										</a>
									</li>

									<li>
										<a data-grid-calendar-preset="month">
											<i class="fa fa-calendar"></i> {{{ trans('date.month') }}}
										</a>
									</li>

								</ul>

								<button class="btn btn-default hidden-xs" type="button" data-grid-calendar data-range-filter="created_at">
									<i class="fa fa-calendar"></i>
								</button>

							</span>

							<input class="form-control" name="filter" type="text" placeholder="{{{ trans('common.search') }}}">

							<span class="input-group-btn">

								<button class="btn btn-default" type="submit">
									<span class="fa fa-search"></span>
								</button>

								<button class="btn btn-default" data-grid-reset>
									<i class="fa fa-refresh fa-sm"></i>
								</button>

							</span>

						</div>

					</form>

				</div>

			</div>

		</nav>

	</header>

	<div class="panel-body">

		{{-- Grid: Applied Filters --}}
		<div class="btn-toolbar" role="toolbar" aria-label="data-grid-applied-filters">

			<div id="data-grid_applied" class="btn-group" data-grid-layout="filters"></div>

		</div>

	</div>

	{{-- Grid: Table --}}
	<div class="table-responsive">

		<table id="data-grid" class="table table-hover" data-grid-source="{{ route('admin.menus.grid') }}">
			<thead>
				<tr>
					<th><input data-grid-checkbox="all" type="checkbox"></th>
					<th class="sortable" data-grid-sort="name">{{{ trans('platform/menus::model.general.name') }}}</th>
					<th class="sortable hidden-xs" data-grid-sort="slug">{{{ trans('platform/menus::model.general.slug') }}}</th>
					<th class="sortable" data-grid-sort="items_count">{{{ trans('platform/menus::model.general.items_count') }}}</th>
					<th class="sortable" data-grid-sort="enabled">{{{ trans('platform/menus::model.general.enabled') }}}</th>
					<th class="sortable hidden-xs" data-grid-sort="created_at">{{{ trans('model.created_at') }}}</th>
				</tr>
			</thead>
			<tbody data-grid-layout="results"></tbody>
		</table>

	</div>

	<footer class="panel-footer clearfix">

		{{-- Grid: Pagination --}}
		<div id="data-grid_pagination" data-grid-layout="pagination"></div>

	</footer>

	{{-- Grid: templates --}}
	@include('platform/menus::grid/index/results')
	@include('platform/menus::grid/index/pagination')
	@include('platform/menus::grid/index/filters')

</section>

@help('platform/menus::help')

@stop
