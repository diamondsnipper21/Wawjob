<div class="title-section">
	<span class="title">
		<i class="icon-info title-icon"></i>
		{{ trans('page.user.detail.title') }}
	</span>
	@if ( !$current_user->isSuspended() )
	<div class="right-action-link">
		<a href="#" class="edit-action" data-toggle="modal" data-target=".modal-edit-company"><i class="icon-pencil"></i></a>
	</div>
	@endif
</div>

<div class="page-content-section user-contact-info-page">
@if ( $user->company != null ) 
	<div class="form-section">
		{{ show_messages() }}
		<fieldset>
				{{-- Company Name --}}
				<div class="form-group">
					<div class="col-sm-3 col-xs-4 text-right">
						<div class="pre-summary">{{ trans('common.company_name') }}</div>
					</div>
					<div class="col-sm-9 col-xs-8">
							<div class="info-div">{{ ($user->company->name != null) ? $user->company->name : '' }}</div>
					</div>
					<div class="clear-div"></div>
				</div>
				{{-- Website --}}
				<div class="form-group">
					<div class="col-sm-3 col-xs-4 text-right">
						<div class="pre-summary">{{ trans('common.website') }}</div>
					</div>
					<div class="col-sm-9 col-xs-8">
						<div class="info-div">{{ ($user->company->website != null) ? $user->company->website : ''}}</div>
					</div>
					<div class="clear-div"></div>
				</div>
				
				{{-- Tagline --}}
				<div class="form-group">
					<div class="col-sm-3 col-xs-4 text-right">
						<div class="pre-summary">{{ trans('common.tagline') }}</div>
					</div>
					<div class="col-sm-9 col-xs-8">
						<div class="info-div">{{ ($user->company->tagline != null) ? $user->company->tagline : '' }}</div>
					</div>
					<div class="clear-div"></div>
				</div>

				{{-- Address --}}
				@if ($user->company->address1 || $user->contact->state || $user->contact->city || $user->contact->country)
				<div class="form-group">
					<div class="col-sm-3 col-xs-4 text-right">
						<div class="pre-summary">{{ trans('common.address') }}</div>
					</div>
					<div class="col-sm-9 col-xs-8">
						@if ($user->company->address1)
						<div class="info-div">{{ $user->company->address1 }}</div>
						@endif

						@if ($user->company->address2)
						<div class="info-div">{{ $user->company->address2 }}</div>
						@endif

						@if ($user->contact->state || $user->contact->city)
						<div class="info-div">
							{{ $user->contact->state?$user->contact->state . ', ': '' }}{{ $user->contact->city ? $user->contact->city : '' }}
						</div>
						@endif

						@if ( $user->contact->country )
						<div class="info-div">
							{{ $user->contact->country->name }}
						</div>
						@endif
					</div>
					<div class="clearfix"></div>
				</div>
				@endif

				{{-- Phone --}}
				@if ($user->company->phone)
				<div class="form-group">
					<div class="col-sm-3 col-xs-4 text-right">
						<div class="pre-summary">{{ trans('common.phone') }}</div>
					</div>
					<div class="col-sm-9 col-xs-8">
						<div class="info-div">{{ $user->company->fullphone() }}</div>
						<div class="clearfix"></div>
					</div>
				</div>
				@endif
		</fieldset>
	</div>
@endif
</div>

@if ( !$current_user->isSuspended() )
	@include('pages.user.contact_info.company.modal')
@endif