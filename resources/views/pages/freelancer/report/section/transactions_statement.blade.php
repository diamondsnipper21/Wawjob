<div class="statement-section row">
	<div class="col-md-offset-8 col-md-4 col-sm-offset-6 col-sm-6 col-xs-12">
		<div class="statement-label text-right">
			<div><strong>{{ trans('report.statement_period') }}</strong></div>
			<div>{{ date($format_date2, strtotime($dates['from'])) . ' - ' . date($format_date2, strtotime($dates['to'])) }}</div>
		</div>
		<div class="statement-content">
			<table class="">
				<tbody>
					<tr>
						<td class="info-label"><strong>{{ trans('report.beginning_balance') }}</strong></td>
						<td class="amount"><strong>
							{{ $statement['beginning'] < 0 ? '(' . formatCurrency(abs($statement['beginning']), $currency_sign) . ')' : formatCurrency($statement['beginning'], $currency_sign) }}
						</strong></td>
					</tr>
					<tr>
						<td class="info-label">{{ trans('report.total_debits') }}</td>
						<td class="amount">
							{{ $statement['debits'] < 0 ? '(' . formatCurrency(abs($statement['debits']), $currency_sign) . ')' : formatCurrency($statement['debits'], $currency_sign) }}
						</td>
					</tr>
					<tr>
						<td class="info-label">{{ trans('report.total_credits') }}</td>
						<td class="amount">
							{{ $statement['credits'] < 0 ? '('.formatCurrency(abs($statement['credits']), $currency_sign) . ')' : formatCurrency($statement['credits'], $currency_sign) }}
						</td>
					</tr>
					<tr>
						<td class="info-label">{{ trans('report.total_change') }}</td>
						<td class="amount">
							{{ $statement['change'] < 0 ? '(' . formatCurrency(abs($statement['change']), $currency_sign) . ')' : formatCurrency($statement['change'], $currency_sign) }}
						</td>
					</tr>
					<tr>
						<td class="info-label last"><strong>{{ trans('report.ending_balance') }}</strong></td>
						<td class="amount last"><strong>
							{{ $statement['ending'] < 0 ? '(' . formatCurrency(abs($statement['ending']), $currency_sign) . ')' : formatCurrency($statement['ending'], $currency_sign) }}
						</strong></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>