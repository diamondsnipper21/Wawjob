<div class="title-section">
	<span class="title">{{ trans('page.user.location.title') }}</span>
	@if ( !$current_user->isSuspended() )
	<div class="right-action-link">
		<a href="#" class="edit-action" data-toggle="modal" data-target=".modal-edit-location"><i class="icon-pencil"></i></a>
		<a href="#" class="cancel-action">{{ trans('common.cancel') }}</a>
	</div>
	@endif
</div>
<div class="page-content-section user-contact-info-page">
	<div class="form-section">
		{{ show_messages() }}
		<fieldset>
				{{-- Address --}}
				<div class="form-group">
					<div class="col-sm-3 col-xs-4 control-label">
						<div class="pre-summary">{{ trans('user.location.address') }}</div>
					</div>
					<div class="col-sm-9 col-xs-8">
						@if ($user->contact->city)
						<div class="info-div">{{ $user->contact->city }}</div>
						@endif

						@if ($user->contact->state || $user->contact->address)
						<div class="info-div">
							{{ $user->contact->state?$user->contact->state . ', ': '' }}{{ $user->contact->address ? $user->contact->address : '' }}
						</div>
						@endif

						@if ( $user->contact->country )
						<div class="info-div">
							{{ $user->contact->country->name }}
						</div>
						@endif
					</div>
					<div class="clear-div"></div>
				</div>
				
				{{-- Phone --}}
				<div class="form-group">
					<div class="col-sm-3 col-xs-4 control-label">
						<div class="pre-summary">{{ trans('user.location.phone') }}</div>
					</div>
					<div class="col-sm-9 col-xs-8">
						<div class="info-div">{{ ($user->contact->phone != null) ? $user->contact->phone : '' }}</div>
						<div class="clear-div"></div>
					</div>
				</div>

				{{-- Timezone --}}
				<div class="form-group">
					<div class="col-sm-3 col-xs-4 control-label">
						<div class="pre-summary">{{ trans('user.location.timezone') }}</div>
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
	</div>
</div>

@if ( !$current_user->isSuspended() )
	@include('pages.user.contact_info.location.modal')
@endif