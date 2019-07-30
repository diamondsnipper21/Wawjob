<?php
/**
 * @author KCG
 * @since Feb 22, 2018
 */

use iJobDesk\Models\File;
use iJobDesk\Models\ProjectApplication;

$application 	= $thread->application;
$job 			= $application->project;
$contract 		= $application->contract;

?>
<div class="inner p-0 px-4">
	<div class="application">
		<div class="proposal-title">{{ trans($contract && !$contract->isOffer()?'common.contract_detail':'common.proposal_detail') }}</div>
		<div class="application-detail">
			<div class="price">
				@if ($contract && !$contract->isOffer())
					@if ( $contract->isHourly() )
						<div class="bold">${{ formatCurrency($contract->price) }} / {{ trans('common.hour') }}</div>
						<div>
							@if ( $contract->isNoLimit() )
								{{ trans('common.no_limit') }}
							@else
								{{ trans('common.n_hours_week', ['n' => $contract->limit]) }}
							@endif
						</div>
					@else
						<div class="bold">{{ formatCurrency($contract->price, $currency_sign) }}</div>
					@endif
					
				@else
					${{ formatCurrency($application->price) }}
				@endif
			</div>
			@if (!($contract && !$contract->isOffer() && $contract->isHourly()) && $application->duration && $application->duration != ProjectApplication::DUR_NS)
			<div class="duration">
				{{ $application->duration_string() }}
			</div>
			@endif
		</div>
	</div>

	<div class="attachments-title">{{ trans('common.attachments') }}</div>
	<div class="attachments slim-scroll">
		@include('pages.message.partials.attachments.contents')
	</div>
</div>