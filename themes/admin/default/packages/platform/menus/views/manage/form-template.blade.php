<script type="text/template" id="form-template">

	<li data-item-id="<%= slug %>">

		<div class="panel panel-default panel-menu">

			<header class="panel-heading collapsed" data-toggle="collapse" data-target="#panel-<%= slug %>" aria-expanded="false" aria-controls="panel-<%= slug %>">

				<span class="item-handle"><i class="fa fa-arrows-alt"></i></span>

				<span class="item-name" data-item-name="<%= slug %>"><%= name %></span>

				<span class="panel-close small pull-right tip" data-original-title="{{{ trans('action.collapse') }}}"></span>

			</header>

			<div class="panel-body collapse" id="panel-<%= slug %>">

				<div class="row">

					<div class="col-md-12">

						<div class="item-form" data-item-form="<%= slug %>" data-item-parent="<%= parent_id %>">

							<input type="hidden" id="<%= slug %>_current-slug" value="<%= slug %>">

							{{-- Item Details --}}
							<fieldset>

								<legend>
									{{{ trans('platform/menus::model.general.item_details') }}}

									<span class="pull-right" data-toggle-options="<%= slug %>"><i class="fa fa-wrench"></i> {{{ trans('platform/menus::model.general.advanced_settings') }}}</span>

									@if ( ! empty($child))
									<span class="pull-right text-danger" data-item-remove="<%= slug %>"><i class="fa fa-trash"></i> {{{ trans('action.remove') }}}</span>
									@endif

								</legend>

								<div class="row">

									<div class="col-sm-3">

										{{-- Name --}}
										<div class="form-group">

											<label class="control-label" for="<%= slug %>_name">
												<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.name_item_help') }}}"></i>
												{{{ trans('platform/menus::model.general.name_item') }}}
											</label>

											<input class="form-control input-sm" data-item-form="<%= slug %>" type="text" name="children[<%= slug %>][name]" id="<%= slug %>_name" value="<%= name %>">

										</div>

									</div>

									<div class="col-sm-3">

										{{-- Slug --}}
										<div class="form-group">
											<label class="control-label" for="<%= slug %>_slug">
												<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.slug_item_help') }}}"></i>
												{{{ trans('platform/menus::model.general.slug_item') }}}
											</label>

											<input class="form-control input-sm" data-item-form="<%= slug %>" type="text" name="children[<%= slug %>][slug]" id="<%= slug %>_slug" value="<%= slug %>">

										</div>

									</div>

									<div class="col-sm-2">

										{{-- Item Type --}}
										<div class="form-group">
											<label class="control-label" for="<%= slug %>_type">
												<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.type_help') }}}"></i>
												{{{ trans('platform/menus::model.general.type') }}}
											</label>

											<div class="controls">
												<select data-item-url-type="<%= slug %>" data-item-form="<%= slug %>" name="children[<%= slug %>][type]" id="<%= slug %>_type" class="form-control input-sm">
													@foreach ($types as $type)
													<option value="{{ $type->getIdentifier() }}"{{ ( ! empty($child) ? $child->type : null) == $type->getIdentifier() ? ' selected="selected"' : null }}>{{ $type->getName() }}</option>
													@endforeach
												</select>
											</div>
										</div>

									</div>

									<div class="col-sm-3">

										{{-- Generate the types inputs --}}
										@foreach ($types as $type)
										{{ $type->getTemplateHtml() }}
										@endforeach

									</div>

								</div>

							</fieldset>

							{{-- Options --}}
							<div  class="hide" data-options>

								<fieldset>

									<legend>{{{ trans('platform/menus::model.general.advanced_settings') }}}</legend>

									<div class="row">

										<div class="col-sm-3">

											{{-- Enabled --}}
											<div class="form-group">
												<label class="control-label" for="<%= slug %>_enabled">
													<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.enabled_help') }}}"></i>
													{{{ trans('platform/menus::model.general.enabled') }}}
												</label>

												<div class="controls">
													<select data-item-form="<%= slug %>" name="children[<%= slug %>][enabled]" id="<%= slug %>_enabled" class="form-control input-sm">
														<option value="1"{{ ( ! empty($child) ? $child->enabled : 1) == 1 ? ' selected="selected"' : null }}>{{{ trans('common.enabled') }}}</option>
														<option value="0"{{ ( ! empty($child) ? $child->enabled : 1) == 0 ? ' selected="selected"' : null }}>{{{ trans('common.disabled') }}}</option>
													</select>
												</div>
											</div>

										</div>

										<div class="col-sm-3">

											{{-- Target --}}
											<div class="form-group">

												<label class="control-label" for="children[<%= slug %>][target]">
													<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.target_help') }}}"></i>
													{{{ trans('platform/menus::model.general.target') }}}
												</label>

												<div class="controls">
													<select data-item-form="<%= slug %>" name="children[<%= slug %>][target]" id="<%= slug %>_target" class="form-control">
														<option value="self"<%= target == 'self' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::model.general.targets.self') }}}</option>
														<option value="new_children"<%= target == 'new_children' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::model.general.targets.blank') }}}</option>
														<option value="parent_frame"<%= target == 'parent_frame' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::model.general.targets.parent') }}}</option>
														<option value="top_frame"<%= target == 'top_frame' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::model.general.targets.top') }}}</option>
													</select>

												</div>

											</div>

										</div>

										<div class="col-sm-3">

											{{-- Parent --}}
											<div class="form-group">

												<label class="control-label" for="<%= slug %>_parent">
													<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.parent_help') }}}"></i>
													{{{ trans('platform/menus::model.general.parent') }}}
												</label>

												<div class="controls">
													<select class="form-control input-sm" data-item-form="<%= slug %>" data-parents name="children[<%= slug %>][parent]" id="<%= slug %>_parent"></select>
												</div>

											</div>

										</div>

										<div class="col-sm-3">

											{{-- Secure --}}
											<div class="form-group">
												<label class="control-label" for="<%= slug %>_secure">
													<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.secure_help') }}}"></i>
													{{{ trans('platform/menus::model.general.secure') }}}
												</label>

												<div class="controls">
													<select data-item-form="<%= slug %>" name="children[<%= slug %>][secure]" id="<%= slug %>_secure" class="form-control input-sm">
														<option value=""{{ ( ! empty($child) ? $child->secure : null) === null ? ' selected="selected"' : null }}>{{{ trans('common.inherit') }}}</option>
														<option value="1"{{ ( ! empty($child) ? $child->secure : null) === true ? ' selected="selected"' : null }}>{{{ trans('common.yes') }}}</option>
														<option value="0"{{ ( ! empty($child) ? $child->secure : null) === false ? ' selected="selected"' : null }}>{{{ trans('common.no') }}}</option>
													</select>
												</div>
											</div>

										</div>

									</div>

									<div class="row">

										<div class="col-sm-4">

											{{-- Class --}}
											<div class="form-group">

												<label class="control-label" for="<%= slug %>_class">
													<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.class_help') }}}"></i>
													{{{ trans('platform/menus::model.general.class') }}}
												</label>

												<input data-item-form="<%= slug %>" type="text" name="children[<%= slug %>][class]" id="<%= slug %>_class" class="form-control" value="<%= klass %>">

											</div>

										</div>

										<div class="col-sm-4">

											{{-- Regular Expression --}}
											<div class="form-group">

												<label class="control-label" for="children[<%= slug %>][regex]">
													<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.regex_help') }}}"></i>
													{{{ trans('platform/menus::model.general.regex') }}}
												</label>

												<input data-item-form="<%= slug %>" type="text" name="children[<%= slug %>][regex]" id="<%= slug %>_regex" class="form-control" value="<%= regex %>">

											</div>

										</div>

										<div class="col-sm-4">

											{{-- Visibility --}}
											<div class="form-group">

												<label class="control-label" for="children[<%= slug %>][visibility]">
													<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('platform/menus::model.general.visibility_help') }}}"></i>
													{{{ trans('platform/menus::model.general.visibility') }}}
												</label>

												<div class="controls">
													<select data-item-form="<%= slug %>" data-item-visibility="<%= slug %>" name="children[<%= slug %>][visibility]" id="<%= slug %>_visibility" class="form-control">
														<option value="always"<%= visibility == 'always' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::model.general.visibilities.always') }}}</option>
														<option value="logged_in"<%= visibility == 'logged_in' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::model.general.visibilities.logged_in') }}}</option>
														<option value="logged_out"<%= visibility == 'logged_out' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::model.general.visibilities.logged_out') }}}</option>
														<option value="admin"<%= visibility == 'admin' ? ' selected="selected"' : null %>>{{{ trans('platform/menus::model.general.visibilities.admin') }}}</option>
													</select>
												</div>

											</div>

										</div>

									</div>

								</fieldset>

							</div>

						</div>

					</div>

				</div>

			</div>

		</div>

	</li>

</script>
