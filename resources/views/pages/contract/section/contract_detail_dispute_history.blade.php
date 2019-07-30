<?php

use iJobDesk\Models\Ticket;

?>
<div class="modal fade" id="modal_dispute_history" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">History</h4>
			</div>
			<div class="modal-body">
				<div class="content-section">
					<table class="table">
						<thead>
							<tr>
								<th>Type</th>
								<th>Settlement</th>
								<th>Initiator</th>
								<th>Start Date</th>
								<th>End Date</th>
							</tr>
						</thead>
						<tbody>
						@forelse ($solved_tickets as $t)
							<tr>
								<td>{{ Ticket::getOptions('result')[$t->archive_type] }}</td>
								<td>{{ $t->reason }}</td>
								<td>{{ $t->user->fullname() }}</td>
								<td>{{ getFormattedDate($t->created_at) }}</td>
								<td>{{ getFormattedDate($t->ended_at) }}</td>
							</tr>
						@empty
							<tr>
								<td colspan="5" align="center">No Histories</td>
							</tr>
						@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
