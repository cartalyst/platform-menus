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
	$.datagrid('main', '#grid', '.pagination', '.applied', {
		loader: '.table-processing',
		sort: {
			column: 'created_at',
			direction: 'desc'
		},
		callback: function(totalCount, filteredCount){
			//Leverage the Callback to show total counts or filtered count
			$('.filtered').html(filteredCount);
		}
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
			@widget('platform/menus::nav.show', array(2, 1, 'nav nav-pills', admin_uri()))
		</nav>
	</header>

	<hr>

	<section class="content">

		<div class="clearfix">

			<form method="post" action="" accept-charset="utf-8" data-search data-grid="main" class="form-inline filters pull-left">

				<select name="column" class="input-medium">
					<option value="all">@lang('general.all')</option>
					<option value="name">@lang('platform/menus::table.name')</option>
					<option value="slug">@lang('platform/menus::table.slug')</option>
				</select>

				<div class="input-append">
					<input name="filter" type="text" placeholder="Search" class="input-large">
					<span class="add-on filtered"></span>
					<button class="btn btn-large"><i class="icon-plus"></i></button>
					<button class="btn btn-large" data-reset data-grid="main"><i class="icon-refresh"></i></button>
					<a class="btn btn-large" href="{{ URL::toAdmin('menus/create') }}">@lang('button.create')</a>
				</div>
			</form>
			<div class="processing pull-left">
				<div class="table-processing" style="display: none;">Processing...</div>
			</div>

		</div>

		<div id="table">

			<div class="tabbable tabs-right">

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

					<ul class="applied" data-grid="main">
						<li data-template style="display:none" class="btn-group">
							<a class="btn" href="#">
								[? if column == undefined ?]
								[[ valueLabel ]]
								[? else ?]
								[[ valueLabel ]] in [[ columnLabel ]]
								[? endif ?]
							</a>
							<a href="#" class="btn remove-filter"><i class="icon-remove-sign"></i></a>
						</li>
					</ul>

					<table id="grid" data-source="{{ URL::toAdmin('menus/grid') }}" data-grid="main" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th data-sort="name" data-grid="main" class="sortable">@lang('platform/menus::table.name')</th>
								<th data-sort="slug" data-grid="main" class="sortable">@lang('platform/menus::table.slug')</th>
								<th data-sort="children_count" data-grid="main" class="span2 sortable">@lang('platform/menus::table.children_count')</th>
								<th data-sort="created_at" data-grid="main" class="sortable">@lang('platform/menus::table.created_at')</th>
								<th class="span1">@lang('table.actions')</th>
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
										<a href="{{ URL::toAdmin('menus/edit/[[ id ]]') }}" class="btn" title="@lang('button.edit')">
											<i class="icon-edit"></i>
										</a>

										<a data-toggle="modal" data-target="#platform-modal-confirm" href="{{ URL::toAdmin('menus/delete/[[ id ]]') }}" class="btn btn-danger" title="@lang('button.delete')">
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
