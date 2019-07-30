<div class="title-section">
	<span class="title">
		<i class="icon-phone title-icon"></i>
		{{ trans('page.user.contact.title') }}
	</span>
	@if ( !$current_user->isSuspended() )
	<div class="right-action-link">
		<a href="#" class="edit-action" data-toggle="modal" data-target=".modal-edit-company"><i class="icon-pencil"></i></a>
	</div>
	@endif
</div>
<div class="page-content-section user-contact-info-page">
	<div class="form-section">
		{{ show_messages() }}
		<fieldset>
				{{-- Address --}}
				@if ($user->company_contact->address || $user->company_contact->state || $user->company_contact->city || $user->company_contact->country)
				<div class="form-group">
					<div class="col-sm-3 col-xs-4 text-right">
						<div class="pre-summary">{{ trans('common.address') }}</div>
					</div>
					<div class="col-sm-9 col-xs-8">
						@if ($user->company_contact->address)
						<div class="info-div">{{ $user->company_contact->address }}</div>
						@endif

						@if ($user->company_contact->state || $user->company_contact->city)
						<div class="info-div">
							{{ $user->company_contact->state?$user->company_contact->state . ', ': '' }}{{ $user->company_contact->city ? $user->company_contact->city : '' }}
						</div>
						@endif

						@if ( $user->company_contact->country )
						<div class="info-div">
							{{ $user->company_contact->country->name }}
						</div>
						@endif
					</div>
					<div class="clearfix"></div>
				</div>
				@endif

				{{-- Phone --}}
				@if ($user->company_contact->phone)
				<div class="form-group">
					<div class="col-sm-3 col-xs-4 text-right">
						<div class="pre-summary">{{ trans('common.phone') }}</div>
					</div>
					<div class="col-sm-9 col-xs-8">
						<div class="info-div">{{ $user->company_contact->fullphone() }}</div>
						<div class="clearfix"></div>
					</div>
				</div>
				@endif
				
				{{-- Timezone --}}
				@if ($user->company_contact->timezone)
				<div class="form-group">
					<div class="col-sm-3 col-xs-4 text-right">
						<div class="pre-summary">{{ trans('common.timezone') }}</div>
					</div>

					<div class="col-sm-9 col-xs-8">
						<div class="info-div">{{ $user->company_contact->timezone->label }}</div>
					</div>

					<div class="clearfix"></div>
				</div>
				@endif
		</fieldset>
	</div>
</div>

@if ( !$current_user->isSuspended() )
	@include('pages.user.contact_info.company.modal')
@endif