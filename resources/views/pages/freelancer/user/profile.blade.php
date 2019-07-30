<?php
/**
 * Profile Page (user/profile/2)
 *
 * @author  - sogwang
 */

use iJobDesk\Models\ProfileViewHistory;
?>

@extends($current_user && $current_user->isAdmin() ? 'layouts/admin/super/user' : 'layouts/default/index')

@section('content')

<div class="page-content-section freelancer-user-page profile-page">
	<div class="row">
		<div class="col-sm-9">
			<div class="page-content">

				{!! show_warnings() !!}

				@if ( $current_user && $current_user->id == $user->id )
				<div class="title-section">
					<span class="title">{{ trans('page.freelancer.user.my_profile.title') }}</span>
				</div>
				@endif
				<div class="row summary">
					<div class="col-sm-3 col-xs-4 photo-side">
						<img src="{{ avatar_url($user) }}" alt="{{ $user->fullname() }}" class="img-responsive img-circle img-user-avatar" width="150" height="150">
					</div>

					<div class="col-sm-9 col-xs-8">
						<div class="row name mb-4">
							<div class="col-md-9 col-sm-6 user-name">
								{{ $user->fullname() }}
								<span>{{ $user->profile->title }}</span>
							</div>
							<div class="col-md-3 col-sm-6 text-right user-rate">
								@if ( $user->profile && $user->profile->rate > 0 )
			                    	${{ number_format($user->profile->rate, 2, '.', ' ') }}/{{ trans('common.hr')}}
			                    @else
			                    	<label class="profile-rate is-not-described">{{ trans('common.n_a') }}</label>
			                    @endif							
							</div>
						</div>

						<ul class="meta">
							<li class="title"></li>
							<li class="country"><i class="icon-location-pin mr-2"></i>{{ $user->contact->city ? $user->contact->city . ', ' : ''}}{{ $user->contact->country->name }}</li>
							<li class="timezone"><i class="icon-clock mr-2"></i>{{ format_date('g:i a', date('Y-m-d H:i:s'), $user) }} {{ trans('common.local_time') }}</li>
							<li class="skill-button">
							@foreach ($user->skills as $skill)
								<span class="rounded-item">{{ parse_multilang($skill->name, App::getLocale()) }}</span>
							@endforeach
							</li>
						</ul>
					</div>                
				</div>

				<div class="left-detail">
					<div class="overview">
						<div class="profile-desc">
							{!! render_more_less_desc($user->profile->desc, 2000) !!}
						</div>
					</div>
				</div>
			</div>

			{{-- Begin Work History and feedback --}}
			<div id="work_history_feedback" class="page-content">
				<div class="title-section margin-bottom-20">
					<span class="title">{{ trans('profile.work_history_and_feedback') }}</span>

					<form method="post" class="col-md-3 pull-right">
					@if (!$contracts->isEmpty())
						<select class="form-control select2" name="feedback_sort_by" id="feedback_sort_by">
							<option value="newest" {{ $feedback_sort_by == 'newest'?'selected':'' }}>
								{{ trans('common.newest_first') }}
							</option>
							<option value="earning" {{ $feedback_sort_by == 'earning'?'selected':'' }}>
								{{ trans('common.earnings') }}
							</option>
							<option value="review" {{ $feedback_sort_by == 'review'?'selected':'' }}>
								{{ trans('common.review') }}
							</option>
						</select>
					@endif
					</form>

					<div class="clearfix"></div>
				</div>
					
				<div class="block work-history-feedback">
					{{--  begin content  --}}                
					<div class="block-content">
						@include('pages.freelancer.user.work_history_feedback')
					</div>
					{{--  end content --}}
				</div>
				{{-- End Work History and feedback --}}
			</div>

			<!-- Portfolio -->
			@if (!$portfolioes->isEmpty())
			<div id="portfolios" class="page-content">
				<div class="title-section margin-bottom-20">
					<span class="title">{{ trans('common.portfolio') }}</span>

					<form method="post" class="col-md-3 pull-right">
					@if (!$portfolioes->isEmpty())
						<select class="form-control select2 pull-right" name="portfolio_category" id="portfolio_category">
							<option value="">{{ trans('common.show_all_categories') }}</option>
							@foreach ($categories as $category)
							<option value="{{ $category->id }}" {{ $category->id == $portfolio_category?'selected':'' }}>{{ parse_multilang($category->name) }}</option>
							@endforeach
						</select>
					@endif
					</form>
				</div>

				<div class="form-group">
					<div class="col-md-12">
						<div class="row">
							@foreach ($portfolioes as $i => $portfolio)
			                    @include ('pages.freelancer.user.myprofile.portfolio')
			                @endforeach

			                @if ($portfolioes->isEmpty())
			                	<div class="not-found-result text-center col-md-12">
			                		{{ trans('profile.message.No_Portfolios') }}
			                	</div>
			                @else
				                <div class="col-xs-12">
				                	<div class="pull-left mt-3">{!! render_pagination_desc('common.showing', $portfolioes) !!}</div>
				                	<div class="pull-right">
				                		{!! $portfolioes->render() !!}
				                	</div>
				                </div>
				            @endif
		            	</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
			@endif

			<!-- Certification, Employment History, Education and Other Experience -->
			@foreach (['certification', 'employment', 'education', 'experience'] as $var_name)
				<?php
					$collection_var_name = $var_name . 's';
				?>
				@if (!$user->$collection_var_name->isEmpty())
				<div id="{{ $collection_var_name }}" class="page-content" data-var="{{ $var_name }}">
					@include('pages.freelancer.user.myprofile.form')

					<div class="title-section margin-bottom-20">
						<span class="title">{{ trans('profile.' . $var_name) }}</span>
					</div>
					<div class="row">
						@forelse ($user->$collection_var_name as $i => $item)
			                @include ('pages.freelancer.user.myprofile.' . $var_name, [$var_name => $item])
			            @empty
			            	<div class="not-found-result text-center">
			            		{{ trans('profile.message.No_' . ucfirst($collection_var_name)) }}
			            	</div>
			            @endforelse
					</div>
				</div>
				@endif
			@endforeach
		</div>

		<div class="col-sm-3 page-content">
			<div class="right-side">
				<div class="buttons">
				@if ( $current_user )
					@if ( $current_user->id == $user->id )
						<a href="{{ route('user.my_profile') }}?mode=edit" id="send_btn" class="btn btn-primary btn-wide {{ $current_user->isSuspended() ? 'disabled' : '' }}">{{ trans('common.edit') }}</a>
					@else
						@if ( $current_user->isBuyer() )
							<button id="btnInvite" class="btn btn-primary btn-wide btn-invite {{ $current_user->isSuspended() ? 'disabled' : '' }}">
								{{ trans('common.invite_to_job') }}
							</button>
							<a href="{{ _route('job.hire_user', ['uid' => $user->id]) }}" id="send_btn" class="btn btn-normal btn-wide {{ $current_user->isSuspended() ? 'disabled' : '' }}">
								{{ trans('common.hire_now') }}
							</a>
							@if ( $isSaved )
							<button type="button" id="saved_user" class="btn btn-normal btn-wide" disabled><i class="fa fa-heart"></i>&nbsp;&nbsp;{{ trans('common.saved') }}</button>
							@else
							<button type="button" id="saved_user" class="btn btn-normal btn-wide" data-url="{{ route('create.view.history', ['uid'=>$user->id]) }}" {{ $current_user->isSuspended() ? 'disabled' : '' }}><i class="fa fa-heart-o"></i>&nbsp;&nbsp;{{ trans('common.save') }}</button>
							@endif

							<div id="boxInvite" class="box-invite">
								<form class="form-horizontal form-invitation" method="post" action="{{ route('job.send_invitation.ajax') }}">
				  					<input type="hidden" name="_token" value="{{ csrf_token() }}">
				  					<input type="hidden" name="user_id" value="{{ $user->id }}">

									<div class="row">
										<div class="col-xs-10">
											<div class="box-user-info">
												<div class="row">
													<div class="col-sm-2 col-xs-3 avatar">
														<a href="{{ _route('user.profile', [$user->id]) }}"><img alt="{{ $user->fullname() }}" class="img-circle pull-left" src="{{ avatar_url($user) }}" width="30" height="30"></a>
													</div>
													<div class="col-sm-10 col-xs-9 info">
														<a href="{{ _route('user.profile', [$user->id]) }}">{{ $user->fullname() }}</a>
														<span>{{ $user->profile->title }}</span>
													</div>
												</div>
											</div>
										</div>
										<div class="col-xs-2">
											<button type="button" class="close">&times;</button>
										</div>
									</div>

									<div class="box-message">
										<div class="mb-2">
		                                    <select class="form-control select2" id="job_id" name="job_id" data-rule-required="true">
		                                    	<option value="">{{ trans('common.choose_job') }}</option>
		                                    	@if ( count($jobs) )
			                                        @foreach($jobs as $job) 
			                                            <option value="{{ $job->id }}" data-url="{{ _route('job.view', ['id' => $job->id]) }}">{{ $job->subject }}</option>
			                                        @endforeach
		                                        @endif
		                                    </select>
										</div>

										<div class="mt-2 box-ctrl">
											<label>{{ trans('common.message') }}</label>
											<textarea name="invite_message" class="form-control maxlength-handler" maxlength="5000">{!! trans('job.place_holder_invitation_message', ['buyer_name' => $current_user->fullname()]) !!}</textarea>
										</div>

										<div class="mt-2">
											<a href="{{ route('job.create') }}?invite_to={{ $user->id }}" class="btn btn-link btn-create-job">{{ trans('job.create_new_job_and_invite') }}</a><br />
											<button type="button" class="btn btn-primary btn-submit">{{ trans('common.send_invitation') }}</button>
										</div>
									</div>
								</form>
							</div><!-- #boxInvite -->
						@endif
					@endif
				@endif
				</div>

				<div class="result-section">
					<div class="profile-score clearfix">
						<div class="stars" title="{{ $user->stat->score }}" data-toggle="tooltip" data-value="{{ $user->stat->score / 5 * 100 }}%"></div>
						<div class="profile-reviews">
							<strong>{{ $user->stat->total_reviews}}</strong>&nbsp;
							{{ trans('common.reviews') }}
						</div>
					</div>
					<div class="profile-success-percent">
	                    {{ trans('profile.success_percent', ['n' => $user->stat->job_success]) }}
	                    <div style="width: {{ $user->stat->job_success }}%;"></div>
	                </div>
				</div>

				<div class="result-section">
					<div class="title">{{ trans('profile.work_history') }}</div>
					@if ( !$user->profile->hide_earning || ($current_user && $current_user->canSeeUserEarning($user)) )
					<div class="mb-2 ml-3"><strong>${{ formatEarned($user->stat ? $user->stat->earning : 0) }}</strong> {{ trans('common.earned') }}</div>
					@endif
					<div class="worked-hour mb-2 ml-3">{{ trans('profile.n_hours_worked', ['n' => $user->stat ? $user->stat->hours : '']) }}</div>
					<div class="job-count ml-3">{{ trans('profile.n_jobs', ['n' => $user->stat ? $user->stat->contracts : '']) }}</div>
				</div>

				<div class="result-section">
					<div class="title">{{ trans('common.availability') }}</div>
					<div class="mb-2 ml-3">
						@if($user->profile->available == 0)
							{{ trans('common.av_not_available') }}
						@elseif($user->profile->available == 1)
							{{ trans('common.av_less_than_10') }}
						@elseif($user->profile->available == 2)
							{{ trans('common.av_10_to_30') }}
						@elseif($user->profile->available == 3)
							{{ trans('common.av_more_than_30') }}
						@endif
					</div>
				</div>

				<div class="result-section languages-section">
					<div class="title">{{ trans('profile.languages') }}</div>
					@if ($user->profile->en_level) 
                        <div class="mb-2 ml-3">
                        	{{ trans('profile.english') }}: {{ parse_multilang($english_levels[$user->profile->en_level]['name']) }}
                        </div>
                    @endif
					@foreach ($user->languages as $lang)
					<div class="mb-2 ml-3">{{ $lang->name }}</div>
					@endforeach
				</div>  
			</div>   
		</div>      
	</div>
</div>

@endsection