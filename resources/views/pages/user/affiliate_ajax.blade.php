@if ( !count($transactions) )
<tr>
    <td colspan="5">
        <div class="empty-data-section">
        {{ trans('common.no_payments') }}
        </div>
    </td>
</tr>
@else
    @foreach ($transactions as $t)
        <tr>
            <td>{{ $t->id }}</td>
            <td>{{ $t->done_at ? format_date('M d, Y', $t->done_at) : ' - ' }}</td>
            <td>
            @if ( $t->ref_user->isFreelancer() )
                <a href="{{ _route('user.profile', ['uid' => $t->ref_user->id]) }}">{{ $t->ref_user->fullname() }}</a>
            @else
                {{ $t->ref_user->fullname() }}
            @endif
            </td>
            <td>{!! $t->affiliate_description_string() !!}</td>
            <td class="text-right">{{ $t->amount > 0 ? '$' . formatCurrency($t->amount) : '($' . formatCurrency(abs($t->amount)) . ')' }}</td>
        </tr>
    @endforeach
@endif