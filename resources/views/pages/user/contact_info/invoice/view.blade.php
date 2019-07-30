<div class="title-section">
	<span class="title">
		<i class="icon-notebook title-icon"></i>
		{{ trans('page.user.invoice_address.title') }}
	</span>
	@if ( !$current_user->isSuspended() )
	<div class="right-action-link">
		<a href="#" class="edit-action" data-toggle="modal" data-target=".modal-edit-invoice-address"><i class="icon-pencil"></i></a>
		<a href="#" class="cancel-action">{{ trans('common.cancel') }}</a>
	</div>
	@endif
</div>
<div class="page-content-section user-contact-info-page">
	<div class="form-section">
		{{ show_messages() }}
		<fieldset>
			<p>This address will be displayed on the invoice sent to clients. In case, you want to use different invoice address than your account address, you may input the invoice address here.</p>
			{{-- Address --}}
			<div class="form-group">
				<div class="col-sm-3 text-right">
					<div class="pre-summary">{{ trans('common.address') }}</div>
				</div>
				<div class="col-sm-9">
					@if ( $user->contact->invoice_address )
					<div class="info-div">{{ $user->contact->invoice_address }}</div>
					@endif
					<div class="info-div">{{ ($user->contact->invoice_city != null) ? $user->contact->invoice_city : ''}}, {{ ($user->contact->invoice_state != null) ? $user->contact->invoice_state : '' }}</div>
					@if ( $user->contact->invoice_country != null )
					<div class="info-div">{{ ($user->contact->invoice_country->name != null) ? $user->contact->invoice_country->name : '' }}</div>
					@endif
				</div>
			</div>
		</fieldset>
	</div>
</div>

@if ( !$current_user->isSuspended() )
	@include('pages.user.contact_info.invoice.modal')
@endif