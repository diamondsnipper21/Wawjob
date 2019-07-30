<?php
/**
* Job Search Page (search/job)
*
* @author  - so gwang
*/

use iJobDesk\Models\Project;
?>
@if ( !$jobs->isEmpty() )
<div id="results"> 
    @foreach ($jobs as $id => $job) 
    @include ('pages.search.jobInfo')
    @endforeach
</div>
@else
<div class="no-found-message text-center">
    <div class="msg">
        {{ trans('search.no_result') }}
    </div>
</div> 
@endif