@extends('templates/default')

{{-- Page title --}}
@section('title')
@lang('platform/menus::general.title') ::
@parent
@stop

{{-- Queue Assets --}}
{{ Asset::queue('tempo', 'js/vendor/tempo/tempo.js', 'jquery') }}
{{ Asset::queue('data-grid', 'js/vendor/cartalyst/data-grid.js', 'tempo') }}

{{-- Inline Scripts --}}
@section('scripts')
@parent
<script>
jQuery(document).ready(function($){
	$.datagrid('main', '.table', '.pagination', '.applied', {
		loader: '.table-processing'
	});
});
</script>
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

		<div class="clearfix">

			<form method="post" action="" accept-charset="utf-8" data-search data-grid="main" class="form-inline pull-left">
				<select name="column" class="input-medium">
					<option value="all">All</option>
					<option value="name">Name</option>
					<option value="slug">Slug</option>
				</select>
				<input name="filter" type="text" placeholder="Filter All" class="input-large">
				<button class="btn btn-medium">Add Filter</button>
				<button class="btn btn-medium" data-reset data-grid="main">Reset</button>
			</form>

			<div class="processing pull-left">
				<div class="table-processing" style="display: none;">Processing...</div>
			</div>

		</div>

		<ul class="applied" data-grid="main">
			<li data-template>
				<a href="#" class="remove-filter btn btn-small">
					[? if column == undefined ?]
					[[ valueLabel ]]
					[? else ?]
					[[ valueLabel ]] in [[ columnLabel ]]
					[? endif ?]
					<span class="close" style="float: none;">&times;</span>
				</a>
			</li>
		</ul>

		<div id="table">

			<div class="tabbable tabs-right">

				<a href="{{ URL::toAdmin('menus/create') }}" class="btn btn-large btn-primary pull-right create">@lang('button.create')</a>

				<ul class="pagination nav nav-tabs" data-grid="main">
					<li data-template data-if-infiniteload>
						<a href="#" class="goto-page" data-page="[[ page ]]">
							Load More
						</a>
					</li>
					<li data-template data-if-throttle>
						<a href="#" class="goto-page" data-throttle>
							[[ label ]]
						</a>
					</li>
					<li data-template class="[? if active ?]active[? endif ?]">
						<a  href="#" data-page="[[ page ]]" class="goto-page">
							[[ pageStart ]] - [[ pageLimit ]]
						</a>
					</li>
				</ul>

				<div class="tab-content">

					<table class="table table-bordered table-striped" data-grid="main" data-source="{{ URL::toAdmin('menus/grid') }}">
						<thead>
							<tr>
								<th data-sort="name" data-grid="main" class="sortable">@lang('platform/menus::table.name')</th>
								<th data-sort="slug" data-grid="main" class="sortable">@lang('platform/menus::table.slug')</th>
								<th data-sort="children_count" data-grid="main" class="sortable">@lang('platform/menus::table.children_count')</th>
								<th data-sort="created_at" data-grid="main" class="sortable">@lang('platform/menus::table.created_at')</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<tr data-template>
								<td>[[ name ]]</td>
								<td>[[ slug ]]</td>
								<td>[[ children_count ]]</td>
								<td>[[ created_at ]]</td>
								<td>
									<div class="btn-group">
										<a href="{{ URL::toAdmin('menus/edit/[[id]]') }}" class="btn" title="Edit">
											<i class="icon-edit"></i>
										</a>

										<a data-toggle="modal" data-target="#platform-modal-confirm" href="{{ URL::toAdmin('menus/delete/[[id]]') }}" class="btn btn-danger" title="Delete">
											<i class="icon-trash"></i>
										</a>
									</div>
								</td>
							</tr>
						</tbody>
					</table>

				</div>

			</div>

		</div>

	</section>

</section>
@widget('platform/ui::modal.confirm')
@stop
