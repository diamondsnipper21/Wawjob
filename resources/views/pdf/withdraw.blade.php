<table width="100%" cellpadding="4" cellspacing="4">
	<tr>
		<th width="40%">Bank Name</th>
		<td>{{ $bankName }}</td>
	</tr>
	<tr>
		<th>Bank Country</th>
		<td>{{ $bankCountry }}</td>
	</tr>
	<tr>
		<th>Bank Branch</th>
		<td>{{ $bankBranch }}</td>
	</tr>
	<tr>
		<th>Beneficiary Address1</th>
		<td>{{ $beneficiaryAddress1 }}</td>
	</tr>
	<tr>
		<th>Beneficiary Address2</th>
		<td>{{ $beneficiaryAddress2 }}</td>
	</tr>
	<tr>
		<th>Beneficiary Swift Code</th>
		<td>{{ $beneficiarySwiftCode }}</td>
	</tr>
	<tr>
		<th>Account Number</th>
		<td>{{ $ibanAccountNo }}</td>
	</tr>
	<tr>
		<th>Account Name</th>
		<td>{{ $accountName }}</td>
	</tr>
	<tr>
		<th>Withdraw Amount</th>
		<td>${{ formatCurrency($amount) }}</td>
	</tr>
	<tr>
		<th>Withdraw Date</th>
		<td>{{ date('M d, Y H:i A') }}</td>
	</tr>
</table>