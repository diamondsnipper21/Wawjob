@extends('layouts/frontend/index')

@section('css')
<link rel="stylesheet" href="{{ url('assets/styles/frontend/download_tools.css') }}">
@endsection

@section('content')
<div class="page-download-tools default-boxshadow border-box">
	<div class="row">
		<div class="col-md-12">
			<div class="title">
				<h1>{{ trans('page.download_tools.title') }}</h1>
			</div>

			<div class="downloads">
				<div class="mb-4 pb-4">
					<p>{!! trans('page.download_tools.desc', ['link' => '#']) !!}</p>
				</div>

				<div class="">
					<form id="formDownload" class="form-horizontal" method="post" action="{{ route('frontend.download_tools') }}">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<div class="download-icon">
							<i class="et-icon-download"></i>
						</div>

						<div class="row os">
							<div class="col-sm-4 text-center btn-box">
								<button type="submit" class="btn btn-primary" name="windows" value="1"><i class="fa fa-windows"></i>&nbsp;&nbsp;Windows</button>
							</div>
							<div class="col-sm-4 text-center btn-box">
								<button type="submit" class="btn btn-primary" name="mac" value="1"><i class="hs-admin-apple"></i>&nbsp;&nbsp;macOS</button>
							</div>
							<div class="col-sm-4 text-center btn-box">
								<button type="button" class="btn btn-primary" data-toggle="modal" data-target=".modal-download-linux"><i class="hs-admin-linux"></i>&nbsp;&nbsp;Linux</button>
							</div>
						</div>
					</form>
				</div>
			</div>

			<div class="modal fade modal-download-linux" id="modalDownloadLinux" aria-hidden="false">
				<form id="formDownloadLinux" class="form-horizontal" method="post" action="{{ route('frontend.download_tools') }}">
					<input type="hidden" name="_token" value="{{ csrf_token() }}" />

					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								<h4 class="modal-title">{{ trans('page.download_tools.linux_modal.title') }}</h4>
							</div>

							<div class="modal-body">
								<div class="row pb-4">
									<div class="col-md-12 fs-13">
										{!! trans('page.download_tools.linux_modal.desc') !!}
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<div class="form-group row">
											<div class="col-xs-4 control-label">
												<div class="pre-summary">{{ trans('page.download_tools.linux_modal.version') }} <span class="form-required"> *</span></div>
											</div>
											<div class="col-xs-8">
												<select type="text" class="form-control select2" id="linux_version" name="linux_version" data-rule-required="1">
													<option value="">{{ trans('page.download_tools.linux_modal.select_placeholder') }}</option>
													<option value="debian_64">{{ trans('page.download_tools.linux_modal.debian_64') }}</option>
													<option value="rpm_64">{{ trans('page.download_tools.linux_modal.rpm_64') }}</option>
													<option value="debian_32">{{ trans('page.download_tools.linux_modal.debian_32') }}</option>
													<option value="rpm_32">{{ trans('page.download_tools.linux_modal.rpm_32') }}</option>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-primary btn-save">{{ trans('common.download_now') }}</button>
								<button type="button" class="btn btn-link btn-cancel" data-dismiss="modal">{{ trans('common.cancel') }}</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection