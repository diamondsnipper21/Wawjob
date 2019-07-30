<?php

use iJobDesk\Models\HelpPage;

Breadcrumbs::register('home', function($breadcrumbs) {
	$breadcrumbs->push(trans('page.home.title'), route('home'));
});

/* Help Page */
Breadcrumbs::register('help_center', function($breadcrumbs, $page) {
	$title = trans('home.help.title');
	$url   = route('frontend.help');

	if ($page->type == HelpPage::TYPE_FREELANCER) { // General
		$title = trans('home.help.freelancer_title');
		$url   .= '#freelancer_content';
	} elseif ($page->type == HelpPage::TYPE_BUYER)
		$title = trans('home.help.buyer_title');

	$breadcrumbs->push($title, $url);
});

Breadcrumbs::register('help_detail', function($breadcrumbs, $page) {
	$breadcrumbs->parent('help_center', $page);

	$tree = [];
	while (true) {
		$tree[] = [parse_json_multilang($page->title), null/*route('frontend.help.detail', ['id' => $page->id])*/];
		$parent_id = $page->parent_id;

		if ($parent_id == 0)
			break;

		$page = HelpPage::find($parent_id);
	}

	for ($i = count($tree) - 1; $i >= 0; $i--) {
		$t = $tree[$i];
		$breadcrumbs->push($t[0], $t[1]);
	}
});

Breadcrumbs::register('contracts', function($breadcrumbs) {
	$breadcrumbs->parent('home');
	$breadcrumbs->push(trans('page.contract.my_contracts.title'), route('contract.all_contracts'));
});

Breadcrumbs::register('contract_detail', function($breadcrumbs, $contract) {
	$breadcrumbs->parent('contracts');
	$breadcrumbs->push($contract->title, route('contract.contract_view', ['id' => $contract->id]));
});

/***************** For Buyer *******************/
Breadcrumbs::register('job_postings', function($breadcrumbs) {
	$breadcrumbs->parent('home');
	$breadcrumbs->push(trans('page.buyer.job.all_jobs.title'), route('job.all_jobs'));
});

Breadcrumbs::register('job_posting', function($breadcrumbs, $job) {
	$breadcrumbs->parent('job_postings');
	$breadcrumbs->push($job->subject, route('job.overview', ['id' => $job->id]));
});

/**************** For Freelancer *******************/
Breadcrumbs::register('proposals', function($breadcrumbs) {
	$breadcrumbs->parent('home');
	$breadcrumbs->push(trans('page.freelancer.job.my_proposals.title'), route('job.my_proposals'));
});

Breadcrumbs::register('proposal', function($breadcrumbs, $proposal) {
	$breadcrumbs->parent('proposals');
	$breadcrumbs->push($proposal->project->subject, route('job.application_detail', ['id' => $proposal->id]));
});

?>