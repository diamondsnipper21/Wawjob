<?php
/**
 * @author Ro Un Nam
 * @since Dec 13, 2017
 */
use iJobDesk\Models\TransactionLocal;
?>
<tr class="status-{{ strtolower($t->status_string()) }}">
	<td>{{ $t->id }}</td>
	<td>{!! $t->date_string() !!}</td>
	<td>{{ $t->type_string() }}</td>
	<td><div class="break">{!! $t->description_string(true) !!}</div></td>
	<td>{!! $t->payer_string(true) !!}</td>
	<td>{!! $t->receiver_string(true) !!}</td>
	<td class="text-right">{{ $t->amount_string(true, $type == (string)TransactionLocal::TYPE_IJOBDESK_EARNING) }}</td>
</tr>