<form id="frm_header_search" class="form-inline clearfix" action="{{ $current_user && $current_user->isFreelancer() ? route('search.job') : route('search.user') }}" method="get">
    <span class="search-button" data-toggle="dropdown" data-close-others="true">
        <i class="icon-arrow-down"></i>
    </span>
    
    <div class="input-group">
        <input type="text" id="search_keyword" placeholder="{{ $current_user && $current_user->isFreelancer() ? trans('search.find_jobs') : trans('search.find_freelancers') }}" class="form-control" name="q">
        <span class="input-group-btn">
            <button id="btnSearch" class="btn" type="submit">
                <i class="icon icon-magnifier"></i>
            </button>
        </span>
    </div>
	<ul class="dropdown-menu">
        <li class="seperator"><a class="btn btn-link btn-search-freelancers">{{ trans('common.freelancers') }}</a></li>
        <li><a class="btn btn-link btn-search-jobs">{{ trans('common.jobs') }}</a></li>
    </ul>
</form>