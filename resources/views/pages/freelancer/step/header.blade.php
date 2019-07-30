<div class="step-numbers">
	@for ($i = 1; $i < 8; $i++)
	<div class="{{ $i == $step?'current':'' }} {{ $i < $step?'past':'' }} {{ $i > $step?'future':'' }} {{ $i == $step + 1?'next':'' }}"><span>{{ $i }}</span></div>
	@endfor
</div>