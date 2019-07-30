@if ( config('menu.enable_lang_menu') )
<li class="dropdown dropdown-user">
	<a href="#" class="dropdown-toggle user-menu-link" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
		<span class="userbox pull-left margin-top-8">
			<!--  by sg   -->
			@if ($current_user)
				@if ($current_user->getLocale() == null)
					<img class='flag' src="/assets/images/common/lang_flags/en.png"/>
					{{ trans('menu.freelancer_lang_menu.en' . '.title') }}
				@elseif ($current_user->getLocale() == "en")
					<img class='flag' src="/assets/images/common/lang_flags/en.png"/>
					{{ trans('menu.freelancer_lang_menu.' . $current_user->getLocale() . '.title') }}  
				@elseif ($current_user->getLocale() == "ch")
					<img class='flag' src="/assets/images/common/lang_flags/ch.png"/>
					{{ trans('menu.freelancer_lang_menu.' . $current_user->getLocale() . '.title') }}
				@elseif ($current_user->getLocale() == "kp")
					<img class='flag' src="/assets/images/common/lang_flags/kp.png"/>
					{{ trans('menu.freelancer_lang_menu.' . $current_user->getLocale() . '.title') }}
				@endif
			@endif
			<!--  end sg   -->
			<i class="fa fa-angle-down"></i>
		</span>
	</a>
	<ul class="dropdown-menu languages">
		@if ($lang_menu)
			@foreach ($lang_menu as $root_key => $root)
				@if ($root['route'] == '#')
					<li class="divider"></li>
				@else
					<li>
						<a href="{{ $root['route'] ? $root['route'] : 'javascript:;' }} " style="background-image: url('{{ $root['img'] }}');">{{ trans('menu.freelancer_lang_menu.' . $root_key . '.title') }}</a>
					</li>
				@endif
			@endforeach
		@endif
	</ul>
</li>
@endif