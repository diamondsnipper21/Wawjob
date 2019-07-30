<?php
/**
 * My Profile Page (user/my-profile)
 *
 * @author 	KCG
 * @since 	Feb 1, 2018
 */

use iJobDesk\Models\User;
use iJobDesk\Models\UserProfile;
use iJobDesk\Models\Category;
use iJobDesk\Models\Language;
use iJobDesk\Models\Skill;

?>
@extends('layouts/user/index')

@section('content')

<div id="my_profile_page" class="profile-page">
	<!-- About Me -->
	<div id="about_me" class="page-content">
		
		{{ show_warnings() }}
		{{ show_messages() }}

		<form action="{{ route('user.my_profile') }}" method="post" class="form-horizontal">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<input type="hidden" name="_action" value="SAVE" />
			
			<div class="title-section margin-bottom-20">

				<div class="row">
			        <div class="col-sm-8">
			            <span class="title">{{ trans('profile.about_me') }}</span>
			        </div>
			        <div class="col-sm-4 text-right">
			        	<div class="row toolbar">
					        <div class="col-sm-12">
					        	<div class="buttons">
					        		<a class="edit-action action-link"><i class="icon-pencil"></i></a>
					        		<a class="btn btn-primary btn-edit-action pull-right">{{ trans('common.edit') }}</a>
						            <a class="btn btn-link cancel-action pull-right" href="{{ route('user.my_profile') }}">{{ trans('common.cancel') }}</a>
						            <button type="submit" class="btn btn-primary save-action pull-right">{{ trans('common.save') }}</button>
					        	</div>
					        </div>
					    </div><!-- .toolbar -->
			        </div>
			    </div>
			</div>

			@include ('pages.freelancer.user.myprofile.about_me')
		</form>
	</div>

	<!-- Portfolio -->
	<div id="portfolios" class="page-content {{ $current_user->isSuspended()?'disable-edit-suspended':'' }}" data-var="portfolio">
		@include('pages.freelancer.user.myprofile.form')

		<div class="title-section margin-bottom-20">
			<span class="title">{{ trans('common.portfolio') }}</span>
			<a href="#" class="pull-right action-link add-item-action"><i class="icon-plus"></i></a>
		</div>
		<div class="form-group">
			<script type="text/javascript">
				var portfolios = @json($portfolios->items());
			</script>
			<div class="col-sm-12">
				<div class="row">
					@forelse ($portfolios as $i => $portfolio)
	                    @include ('pages.freelancer.user.myprofile.portfolio')
	                @empty
	                	<div class="not-found-result text-center col-sm-12">
	                		{{ trans('profile.message.No_Portfolios') }}
	                	</div>
	                @endforelse

	                <!-- Pagination -->
	                @if (!$portfolios->isEmpty())
	                <div class="col-sm-12">
	                	<div class="pull-left mt-2">{!! render_pagination_desc('common.showing', $portfolios) !!}</div>
	                	<div class="pull-right">
	                		{!! $portfolios->render() !!}
	                	</div>
	                </div>
	                @endif
            	</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>

	<!-- Certification, Employment History, Education and Other Experience -->
	@foreach (['certification', 'employment', 'education', 'experience'] as $var_name)
	<?php
		$collection_var_name = $var_name . 's';
	?>
	<div id="{{ $collection_var_name }}" class="page-content {{ $current_user->isSuspended()?'disable-edit-suspended':'' }}" data-var="{{ $var_name }}">
		@include('pages.freelancer.user.myprofile.form')

		<div class="title-section margin-bottom-20">
			<span class="title">{{ trans('profile.' . $var_name) }}</span>
			<a href="#" class="pull-right action-link add-item-action"><i class="icon-plus"></i></a>
		</div>
		<div class="form-group">
			<script type="text/javascript">
				var {{ $collection_var_name }} = @json($user->$collection_var_name);
			</script>
			@forelse ($user->$collection_var_name as $i => $item)
                @include ('pages.freelancer.user.myprofile.' . $var_name, [$var_name => $item])
            @empty
            	<div class="not-found-result text-center">
            		{{ trans('profile.message.No_' . ucfirst($collection_var_name)) }}
            	</div>
            @endforelse
		</div>
	</div>
	@endforeach
</div>

@include ('pages.freelancer.user.myprofile.modals', ['modal_form_action' => route('user.my_profile.add')])

@endsection