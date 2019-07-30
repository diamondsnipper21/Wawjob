<?php
/**
 * My Profile Page (my-profile)
 *
 * @author 	KCG
 * @since 	Feb 1, 2018
 */

use iJobDesk\Models\User;

?>
<script type="text/javascript">
	var AVATAR_WIDTH  	  = {{ User::AVATAR_WIDTH }};
	var AVATAR_HEIGHT 	  = {{ User::AVATAR_HEIGHT }};
</script>

<div class="title-section">
	<span class="title">
		<i class="icon-user title-icon"></i>
		{{ trans('common.my_account') }}
	</span>
	@if ( !$current_user->isSuspended() )
	<div class="right-action-link">
		<a href="#" class="edit-action" data-toggle="modal" data-target=".modal-edit-account"><i class="icon-pencil"></i></a>
		<a href="#" class="cancel-action">{{ trans('common.cancel') }}</a>
	</div>
	@endif
</div>

<div class="page-content-section user-contact-info-page">

	<div class="row pb-4">
		<div class="col-md-12 info">
			{!! trans('user.note_invisible_contact_fields') !!}
		</div>
	</div>
	<div class="form-section">

		{{ show_messages() }}

		<fieldset>
			{{-- Photo --}}
			<img src="{{ avatar_url($user) }}" width="100" height="100" class="user-avatar img-circle img-responsive" />

			{{-- User ID --}}
			<div class="form-group">
				<div class="col-sm-3 col-xs-4 text-right">
					<div class="pre-summary">{{ trans('common.user_id') }}</div>
				</div>
				<div class="col-sm-9 col-xs-8">
					<div class="info-div">{{ ($user->username != null)? $user->username : "" }}</div>
				</div>
				<div class="clear-div"></div>
			</div>

			{{-- Username --}}
			@if ( !$user->isCompany() )
			<div class="form-group">
				<div class="col-sm-3 col-xs-4 text-right">
					<div class="pre-summary">{{ trans('common.name') }}</div>
				</div>
				<div class="col-sm-9 col-xs-8">
					<div class="info-div">{{ ($user->contact->first_name != null || $user->contact->last_name != null)? $user->contact->first_name .' '. $user->contact->last_name: "" }}</div>
				</div>
				<div class="clear-div"></div>
			</div>
			@endif

			{{-- Type --}}
			<div class="form-group">
				<div class="col-sm-3 col-xs-4 text-right">
					<div class="pre-summary">{{ trans('common.type') }}</div>
				</div>
				<div class="col-sm-9 col-xs-8">
					<div class="info-div">{{ $user->is_company == 0?__('common.individual'):__('common.company') }}</div>
				</div>
				<div class="clear-div"></div>
			</div>

			{{-- Email --}}
			<div class="form-group">
				<div class="col-sm-3 col-xs-4 text-right">
					<div class="pre-summary">{{ trans('common.email') }}</div>
				</div>
				<div class="col-sm-9 col-xs-8">
					<div class="info-div">{{ ($user->email != null)? $user->email : "" }}</div>
				</div>
				<div class="clear-div"></div>
			</div>
			
			{{-- Address --}}
			<div class="form-group">
				<div class="col-sm-3 col-xs-4 text-right">
					<div class="pre-summary">{{ trans('common.address') }}</div>
				</div>
				<div class="col-sm-9 col-xs-8">
					@if ( $user->contact->address )
					<div class="info-div">{{ $user->contact->address }}</div>
					@endif
					@if ( $user->contact->address2 )
					<div class="info-div">{{ $user->contact->address2 }}</div>
					@endif
					<div class="info-div">{{ ($user->contact->city != null) ? $user->contact->city.',' : ''}} {{ ($user->contact->state != null) ? $user->contact->state : '' }}</div>
					@if ( $user->contact->country != null )
					<div class="info-div">{{ ($user->contact->country->name != null) ? $user->contact->country->name : '' }}</div>
					@endif
				</div>
				<div class="clear-div"></div>
			</div>			

			{{-- Phone --}}
			<div class="form-group">
				<div class="col-sm-3 col-xs-4 text-right">
					<div class="pre-summary">{{ trans('common.phone') }}</div>
				</div>
				<div class="col-sm-9 col-xs-8">
					<div class="info-div">{{ ($user->contact->phone != null) ? $user->contact->fullphone() : '' }}</div>
					<div class="clear-div"></div>
				</div>
				<div class="clear-div"></div>
			</div>

			{{-- Timezone --}}
			<div class="form-group">
				<div class="col-sm-3 col-xs-4 text-right">
					<div class="pre-summary">{{ trans('common.timezone') }}</div>
				</div>
				<div class="col-sm-9 col-xs-8">
					@if ( $user->contact->timezone != null )
						<div class="info-div">{{ ($user->contact->timezone->label != null) ? $user->contact->timezone->label : '' }}</div>
					@else
						<div class="info-div"></div>
					@endif					
				</div>
				<div class="clear-div"></div>
			</div>
		</fieldset>
		@if ( !$current_user->isSuspended() )
		<div class="text-right">
			<a href="{{ route('user.close_my_account') }}" class="close-my-account">{{ trans('common.close_my_account') }}</a>
		</div>
		@endif
		<!-- </form> -->
	</div>
</div>
@if ( !$current_user->isSuspended() )
	@include('pages.user.contact_info.account.modal')
@endif