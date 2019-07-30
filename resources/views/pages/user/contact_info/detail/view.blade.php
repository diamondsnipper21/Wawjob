<div class="title-section">
	<span class="title">
		<i class="icon-info title-icon"></i>
		{{ trans('page.user.detail.title') }}
	</span>
	@if ( !$current_user->isSuspended() )
	<div class="right-action-link">
		<a href="#" class="edit-action" data-toggle="modal" data-target=".modal-edit-detail"><i class="icon-pencil"></i></a>
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

				{{-- Description --}}
				<div class="form-group">
					<div class="col-sm-3 col-xs-4 text-right">
						<div class="pre-summary">{{ trans('common.description') }}</div>
					</div>
					<div class="col-sm-9 col-xs-8">
						<div class="info-div">{!! render_more_less_desc($user->company->description) !!}</div>
					</div>
					<div class="clear-div"></div>
				</div>
		</fieldset>
	</div>
@endif
</div>

@if ( !$current_user->isSuspended() )
	@include('pages.user.contact_info.detail.modal')
@endif