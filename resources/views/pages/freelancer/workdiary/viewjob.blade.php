<?php
use iJobDesk\Models\Project;
use iJobDesk\Models\Contract;
?>
@extends($current_user->isAdmin()?'layouts/admin/super/user':'layouts/default/index')

@section('content')
<script type="text/javascript">
	var log_dates = @json($log_dates);
	var url_workdiary_action = '{{ route('workdiary.ajaxjob') }}';
</script>

<div class="title-section">
    <span class="title">
    	<span><i class="icon-screen-desktop title-icon"></i>
    	{{ trans('page.' . $page . '.title') }}</span>
    	<span class="admin-title hide caption-subject font-green-sharp bold"><i class="fa icon-calendar font-green-sharp"></i>&nbsp;Workdiary</span>
	</span>
</div>

<div class="page-content-section">
	{{ show_messages() }}

	<div class="workdiary-section">
		@if ( $contract )
		<div class="row">
			<div class="col-md-4 col-sm-3">	
				<div class="row-contract">
					@include('pages.freelancer.workdiary.modal.contract_selector')
				</div><!-- END OF .row-contract -->
			</div>

			<div class="col-md-4 col-sm-6">	
				<div class="form-group col-calendar text-center">
					<a href="{{ $meta['dateUrls']['prev'] }}" class="date-nav prev-date btn btn-link{{ $prev_disabled ? ' disabled' : '' }}"><i class="icon-arrow-left"></i></a>
					<span data-cid="{{ $contract['id'] }}" data-date="{{ $meta['wdate'] }}" class="mr-3 current-date">{{ trans('common.weekdays_abbr.' . date_format(date_create($meta['wdate']), 'N')) }} {{ date_format(date_create($meta['wdate']), $format_date2) }}</span>
					<span class="pointer date-picker" data-date="{{ $meta['wdate'] }}"><i class="fa icon-calendar"></i></span>
					<a href="{{ $meta['dateUrls']['next'] }}" class="date-nav next-date btn btn-link{{ $next_disabled ? ' disabled' : '' }}"><i class="icon-arrow-right"></i></a>
					<a href="{{ $meta['dateUrls']['today'] }}" class="goto-today btn btn-link{{ $today_disabled ? ' disabled' : '' }}">{{ trans('common.today') }}</a>
					<input type="hidden" id="start_date" value="{{ $started_at }}">
				</div>
			</div>

			<div class="col-md-4 col-sm-3">
				<div class="pull-right w-35">
					<select name="wtimezone" class="form-control select2 wtimezone">
						<option value="UTC">UTC</option>
						@foreach ($options['tz'] as $label => $v)
						<option value="{{ $v }}"{{ $v == $meta['tz'] ? ' selected' : ''}}>{{ $label }}</option>
						@endforeach
					</select>
				</div>
				<label class="pull-right mt-2 mr-3 timezone-label">{{ trans('common.timezone') }}</label>				
			</div>
		</div>

		<div class="row row-logmeta">
			<div class="col-md-8 col-xs-6 col-tracked">
				<div class="info-group info-total-time">
					<span>{{ trans('common.total_time_logged') }}:</span> <span class="total-time">{{ $meta['time']['total'] }}</span>
				</div>
				<div class="info-group">
					<span class="rect rect-auto"></span><span>{{ trans('common.auto_tracked') }}:</span> <span class="auto-time">{{ $meta['time']['auto'] }}</span>
				</div>
				<div class="info-group">
					<span class="rect rect-manual"></span><span>{{ trans('common.manual_time') }}:</span> <span class="manual-time">{{ $meta['time']['manual'] }}</span>
				</div>
				<div class="info-group hidden">
					<span class="rect rect-overlimit"></span><span>{{ trans('common.overlimit') }}:</span> <span class="overlimit-time">{{ $meta['time']['overlimit'] }}</span>
				</div>
			</div>

			<div class="col-md-4 col-xs-6 form-group col-viewmode text-right">
				<div class="row">
					<div class="col-sm-8 col-xs-12">
						<div class="info-group pull-right">
							<span class="rect rect-select"></span>
							<span>{{ trans('common.selected') }}: </span><strong><span class="selected-time">00:00</span> min</strong>
						</div>
					</div>
					<div class="col-sm-4 btn-mode-list">
						<div class="btn-group btn-group-solid btn-group-viewmode">
							<button data-mode="grid" type="button" class="btn btn-mode{{ $meta['mode'] == 'grid' ? ' active' : ''}}"><i class="fa fa-th"></i></button>
							<button data-mode="list" type="button" class="btn btn-mode{{ $meta['mode'] == 'list' ? ' active' : ''}}"><i class="fa fa-list"></i></button>
						</div>
					</div>
				</div>
			</div>			
		</div>

		<div class="row row-action">
			<div class="col-xs-12">
				@if ( $is_this_week && !$current_user->isSuspended() )
					@if ($diary)
						<div class="btn-group pull-right">
							<button type="button" class="btn btn-primary require-slots disabled" id="deselectAll" name="deselectall">{{ trans('common.deselect_all') }}</button>
						</div>
						@if (!$current_user->isAdmin())
							<div class="btn-group pull-right">
								<button type="button" class="btn btn-primary require-slots disabled" id="delete" name="delete"{{ !$contract->isOpen() ? ' disabled' : '' }}>{{ trans('common.delete') }}</button>
							</div>
							<div class="btn-group pull-right">
								<button type="button" class="btn btn-primary require-slots disabled" id="editMemo" name="editmemo"{{ !$contract->isOpen() ? ' disabled' : '' }}>{{ trans('common.edit_memo') }}</button>
							</div>
						@endif
					@endif

					@if (!$current_user->isAdmin() && $contract->is_allowed_manual_time)
					<div class="btn-group pull-right">
						<a class="btn btn-primary{{ !$contract->isOpen() ? ' disabled' : '' }}" data-target="#addManualModal" data-toggle="modal" id="addManual" name="addmanual">{{ trans('common.add_manual_time') }}</a>
					</div>
					@endif
				@endif
			</div>
		</div>

		<div class="row-screenshots">
			@if ($diary)
				{{-- Grid mode --}}
				<?php $first_screenshot = true; ?>
				<div class="pane-grid pane"@if ($meta['mode'] == 'list') style="display: none;"@endif>
					@foreach($diary as $hr => $group)
					<div class="row-hour clearfix">
						<div class="ss-hour">
							<span class="num">{{ $group['label']['hour'] }}</span>
							<div class="ampm">{{ $group['label']['ampm'] }}</div>
							<div class="select-box"><input type="checkbox" class="selectable-box select-hour"></div>
						</div>
						<div class="ss-col-right">
							@foreach($group['seg'] as $seg)
							<div class="seg{{ $seg['start'] ? ' start' : ''}}{{ $seg['end'] ? ' end' : ''}} from{{ $seg['from'] }} to{{ $seg['to'] }}{{ $seg['is_manual'] ? ' manual' : ''}}{{ $seg['is_overlimit'] ? ' overlimit' : ''}}">{{ $seg['comment'] ?: trans('common.no_memo') }}</div>
							@endforeach

							<ul class="slots">
								@for ($si = 0; $si < $meta["maxSlot"]; $si++)
								<?php $slot = $group['slots'][$si]; ?>
								<li class="slot clearfix{{ $slot['is_empty'] == false && $slot['is_overlimit'] ? ' overlimit' : ''}}" data-comment="{{ isset($slot['comment']) ? $slot['comment'] : '' }}">
									@if ($slot['is_empty'])
										<div class="pic no-pic"></div>
									@elseif ($slot['is_manual'])
										<div class="pic manual"></div>
									@else
										@if ($first_screenshot)
											<div id="grid-container" class="grid-container hide">
												<div class="cbp-item">
													<a href="{{ $slot['link']['full'] }}" class="link-full cbp-lightbox" data-title="{{ $slot['active_window'] }}">
														<img class="ss" src="{{ $slot['link']['thumbnail'] }}" title="{{ $slot['active_window'] }}">
													</a>
												</div>
											</div>
											<?php $first_screenshot = false; ?>
		                                @endif
		                                <div class="pic{{ isset($slot['link']) ? ' has-pic' : '' }}">
											<div id="grid-container" class="grid-container">
												<div class="cbp-item">
													<a href="{{ $slot['link']['full'] }}" class="link-full cbp-lightbox" data-title="{{ $slot['active_window'] }}">
														<img class="ss" src="{{ $slot['link']['thumbnail'] }}" title="{{ $slot['active_window'] }}">
													</a>
												</div>
											</div>
										</div>
										<div class="score" data-toggle="tooltip" data-placement="left" data-html="true" title="Score: {{ $slot['score'] }}<br>{{ $slot['comment'] }}">
											<div class="grey"></div>
											<div class="green" style="height: {{ $slot['score'] * 11 }}px"></div>
											<div class="borders">
												@for ($bi = 0; $bi < 10; $bi++)<div class="border-block"></div>@endfor
											</div>
											<a data-id="{{ $slot['id'] }}" href="#modalSlot" data-toggle="modal" data-backdrop="static" class="a-slot"></a>
										</div>
									@endif

									<div class="info">
										@if ( !$slot['is_empty'] )
										<span class="select-box margin-right-10"><input type="checkbox" class="selectable-box select-slot" data-id="{{ $slot['id'] }}"></span>
										@endif
										{{ $group["slots"][$si]["timeLabel"] }}
									</div>
								</li>
								@endfor
							</ul>
						</div>
					</div>
					@endforeach
				</div><!-- .pane -->

				{{-- List mode --}}
				<div class="pane-list pane"@if ($meta['mode'] == "grid") style="display: none;"@endif>
					@foreach($diary as $hr => $group)
					<div class="row-hour clearfix">
						<div class="ss-hour">
							<span class="num">{{ $group['label']['hour'] }}</span>
							<div class="ampm">{{ $group['label']['ampm'] }}</div>
							<div class="select-box"><input type="checkbox" class="selectable-box select-hour"></div>
						</div>
						<div class="ss-col-right">
							<ul class="list-slots">
								@for ($si = 0; $si < $meta["maxSlot"]; $si++)
								<?php $slot = $group['slots'][$si]; ?>
								<li class="slot{{ $slot['is_empty'] == false && $slot['is_overlimit'] ? ' overlimit' : ''}}{{ $slot['is_empty'] == false && $slot['is_manual'] ? ' manual' : ''}}" data-comment="{{ isset($slot['comment']) ? $slot['comment'] : '' }}">
									@if (isset($slot['link']))
									<div class="pic" style="display: none;">
										<a href="{{ $slot['link']['full'] }}" target="_blank" class="link-full">
											<img class="ss" src="{{ $slot['link']['thumbnail'] }}">
										</a>
									</div>
									@endif
									@if ( !$slot['is_empty'] )
									<span class="select-box margin-right-10"><input type="checkbox" class="selectable-box select-slot" data-id="{{ $slot['id'] }}"></span>
									@else
									<span class="select-box empty">&nbsp;</span>
									@endif
									<span class="info">{{ $group["slots"][$si]["timeLabel"] }}</span>
									@if ( !$slot['is_empty'] )
									<span class="score iblock" title="{{ trans('common.score') }}: {{ $slot['score'] }}">
										<div class="grey"></div>
										@if ( !$slot['is_manual'] )
										<div class="green" style="width: {{ $slot['score'] * 10 }}%"></div>
										@endif
										<div class="borders">
											@for ($bi = 0; $bi < 10; $bi++)<span class="border-block"></span>@endfor
										</div>
										@if ( !$slot['is_manual'] )
										<a data-id="{{ $slot['id'] }}" href="#modalSlot" data-toggle="modal" data-backdrop="static" class="a-slot"></a>
										@endif
									</span>
									<span class="comment break">{{ $slot['is_empty'] ? '' : ($slot['comment'] != '' ? $slot['comment'] : trans('common.no_memo')) }}</span>
									@if ( !$slot['is_manual'] )
									<span class="active-window-wrp">[<span class="active-window">{{ $slot['active_window'] }}</span>]</span>
									@endif
									@endif
								</li>
								@endfor
							</ul>
						</div>
					</div>
					@endforeach
				</div><!-- .pane -->

			@else
				<div class="not-found-result">
					<div class="row">
						<div class="col-md-12 text-center">
							<div class="heading margin-bottom-40">
								{{ $contract->tracked_time ? trans('common.no_time_logged') : trans('common.no_time_logged_yet') }}
							</div>

							<strong>{{ trans('common.tips') }}:</strong> {{ trans('contract.tip_download_tool_description') }} <a href="{{ route('frontend.download_tools') }}" class="download-link">{{ trans('common.download_now') }}</a>
						</div>
					</div>
				</div>
			@endif
		</div>
		@else
			<div class="not-found-result">
				<div class="row">
					<div class="col-md-12 text-center">
						<div class="heading">{{ trans('contract.you_have_no_contracts') }}</div>
					</div>
				</div>
			</div>
		@endif

	</div>

</div>

@if ( !$current_user->isSuspended() )
	@include('pages.freelancer.workdiary.modal.edit_memo')
	@include('pages.freelancer.workdiary.modal.add_manual')
@endif

@include('pages.freelancer.workdiary.modal.slot')

@endsection