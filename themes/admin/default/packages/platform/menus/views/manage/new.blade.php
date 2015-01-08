<ol>
<li data-item-id="">

	<div class="panel panel-default panel-menu">

		<header class="panel-heading collapsed" data-toggle="collapse" data-target="#panel-{{ $child->id }}" aria-expanded="false" aria-controls="panel-{{ $child->id }}">

			<span class="item-handle"><i class="fa fa-arrows-alt"></i></span>

			<span class="item-name">Add Item</span>


			<!-- <span class="item-status{{ $child->enabled == 0 ? null : ' hide' }}" data-item-status="{{ $child->id }}"><i class="fa fa-eye-slash"></i></span> -->

			<span class="panel-close small pull-right tip" data-original-title="{{{ trans('action.collapse') }}}"></span>

		</header>

		<div class="panel-body collapse" id="panel-{{ $child->id }}">

			<div class="row">

				<div class="col-md-12">

					@include('platform/menus::manage/form')

				</div>

			</div>

		</div>

	</div>

</li>
</ol>
