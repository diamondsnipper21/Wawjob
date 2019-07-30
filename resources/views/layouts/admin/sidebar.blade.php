<div class="navbar-collapse collapse" aria-expanded="false">
  <ul class="nav navbar-nav side-nav">
    @foreach ($sidebar as $root_key => $root)
    <li class="{{ $root['active'] ? 'active' : '' }}">
      <a href="{{ $root['route'] ? route($root['route']) : 'javascript:;' }}"@if ($root['children']) data-toggle="collapse" data-target=".subnav-{{ $root_key }}"@endif>
        @if ($root['icon'])
        <i class="{{ $root['icon'] }}"></i>
        @endif
        <span class="title">{{ trans('menu.sidebar.' . $root_key . '.title') }}</span>
        <span class="selected"></span>
        @if ($root['children'])
        <span class="arrow{{ $root['active'] ? ' active' : '' }}"></span>
        @endif
      </a>
      @if ($root['children'])
      <ul class="subnav-{{ $root_key }} collapse{{ $root['active'] ? ' in' : '' }}">
        @foreach ($root['children'] as $child_key => $child)
        <li class="{{ $child['active'] ? 'active' : '' }}">
          <a href="{{ $child['route'] ? route($child['route']) : 'javascript:;' }}">
            @if ($child['icon'])
            <i class="{{ $child['icon'] }}"></i>
            @endif
            {{ trans('menu.sidebar.' . $root_key . '.' . $child_key . '.title') }}
          </a>
        </li>
        @endforeach
      </ul>
      @endif
    </li>
    @endforeach
  </ul>
</div>