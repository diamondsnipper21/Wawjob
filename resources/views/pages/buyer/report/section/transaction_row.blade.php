<div class="tr status-{{ strtolower($t->status_string()) }}">
	<div class="td rp-ref" style="width:8%">{{ $t->id }}</div>
	<div class="td rp-date" style="width:10%">{!! $t->date_string($format_date2) !!}</div>
	<div class="td rp-type" style="width:12%">{{ $t->type_string() }}</div>
	<div class="td rp-description" style="width:46%"><div class="break">{!! $t->description_string() !!}</div></div>
	<div class="td rp-freelancer" style="width:12%">{!! $t->freelancer_string(Route::currentRouteName() == 'admin.super.user.transactions' ? true : false) !!}</div>
	<div class="td rp-amount text-right" style="width:12%">{{ $t->amount_string() }}</div>
</div>