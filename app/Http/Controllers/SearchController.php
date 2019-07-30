<?php namespace iJobDesk\Http\Controllers;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Auth;
use Storage;
use Config;
use Cache;

// Models
use iJobDesk\Models\User;
use iJobDesk\Models\UserContact;
use iJobDesk\Models\Role;
use iJobDesk\Models\Country;
use iJobDesk\Models\Timezone;
use iJobDesk\Models\Project;
use iJobDesk\Models\Category;
use iJobDesk\Models\ProfileViewHistory;
use iJobDesk\Models\UserStat;

//DB
use DB;

class SearchController extends Controller {

	public $search_title;
	public $arryCountryCode = [];

	/**
	* Constructor
	*/
	public function __construct()
	{
		parent::__construct();
	}

	/**
	* Search the user-informations.
	* @author KCG
	* @param  Request $request
	* @return Response
	*/
	public function user(Request $request) {

		$filtered = false;

		$per_page = Config::get('settings.freelancer.per_page');
			
		// Filtering
		$params = [];

		$params['keyword'] 			= $request->input('q');
		$params['category'] 		= $request->input('c');
		$params['job_success'] 		= $request->input('js');
		$params['hourly_rate'] 		= $request->input('hr');
		$params['hours_billed'] 	= $request->input('hb');
		$params['feedback'] 		= $request->input('f');
		$params['activity'] 		= $request->input('a');
		$params['english_level'] 	= $request->input('el');
		$params['locations'] 		= $request->input('l');
		$params['title'] 			= $request->input('t');
		$params['languages'] 		= $request->input('ln');

		if ( $params['category'] || $params['job_success'] || $params['hourly_rate'] || $params['hours_billed'] || $params['feedback'] || $params['activity'] || $params['english_level'] || $params['locations'] || $params['title'] || $params['languages'] ) {
			
			$filtered = true;
		}

		$request->flash();

		$urlParams = [
			'q' 	=> $params['keyword'],
			'c' 	=> $params['category'],
			'js' 	=> $params['job_success'],
			'hr' 	=> $params['hourly_rate'],
			'hb' 	=> $params['hours_billed'],
			'f' 	=> $params['feedback'],
			'a' 	=> $params['activity'],
			'el' 	=> $params['english_level'],
			'l' 	=> $params['locations'],
			't' 	=> $params['title'],
			'ln' 	=> $params['languages']
		];

		$users = User::searchFreelancers($params, $per_page);

		/*if ( !$users->items() ) {
			return redirect()->route('search.user', ['page' => $users->lastPage()]);
		}*/

		return view('pages.search.user', [
			'page' => 'search.user',
			'users' => $users,
			'filtered' => $filtered,
			'params' => $urlParams,
			'page_route' => route('search.user')
		]);
	}

	/**
	 * @author KCG
	 * @since 20170529
	 * @return The options for feedback on search.
	 */
	private function getFeedbackOptions() {
		return [
			''   	=> trans('common.any_feedback'),
			'4.5' 	=> trans('search.4.5_up_star'),
			'4'   	=> trans('search.4_up_star'),
			'3'   	=> trans('search.3_up_star'),
			'2'   	=> trans('search.2_up_star'),
			'1'   	=> trans('search.1_up_star'),
		];
	}

	/**
	 * @author KCG
	 * @since 20170529
	 * @return The options for job success on search.
	 */
	private function getJobSuccessOptions() {
		return [
			''   => trans('common.any_job_success'),
			'80' => trans('search.80_up_success'),
			'90' => trans('search.90_up_success'),
		];
	}

	/**
	 * @author KCG
	 * @since 20170529
	 * @return The options for hourly rate on search.
	 */
	private function getHourlyRateOptions() {
		return [
			''         => trans('common.any_hourly_rate'),
			'0-10' 		=> trans('search.below_10'),
			'10-30' 	=> trans('search.between_10_30'),
			'30-60' 	=> trans('search.between_30_60'),
			'60-10000' 	=> trans('search.above_60')
		];
	}

	/**
	 * @author KCG
	 * @since 20170530
	 * @return The options for hourly billed on search.
	 */
	private function getHourlyBilledOptions() {
		return [
			''        	=> trans('common.any_hours_billed'),
			'1' 		=> trans('search.below_1_hour'),
			'100' 		=> trans('search.below_100_hour'),
			'1000' 		=> trans('search.below_1000_hour'),
		];
	}

	/**
	 * @author KCG
	 * @since 20170530
	 * @return The options for last activity on search.
	 */
	private function getLastActivityOptions() {
		return [
			'' => trans('common.any_time'),
			date('Y-m-d', strtotime('-2 weeks')) => trans('job.last_active_within_2_weeks'),
			date('Y-m-d', strtotime('-1 month')) => trans('job.last_active_within_1_month'),
			date('Y-m-d', strtotime('-2 months')) => trans('job.last_active_within_2_months'),
		];
	}

	private function getSearchOptions($main_category_id, $items, $fn_name) {
		$user = Auth::user();

		$category_ids = null;
		if (!empty($main_category_id)) {
			$sub_category_ids = Category::where('parent_id', $main_category_id)->pluck('id')->toArray();

			if ($sub_category_ids)
				$category_ids = $sub_category_ids;
			else
				$category_ids = [$main_category_id];
		}

		$options = [];
		foreach ($items as $key => $title) {
			$jobs = Project::open()
						   ->acceptTerm()
						   ->$fn_name($key);

			if (!$user)
				$jobs->publics();
			else
				$jobs->protected();

			if ($category_ids)
				$jobs->whereIn('category_id', $category_ids);

			$count = $jobs->count();

			$options[$key] = [
				'title' => $title,
				'count' => $count,
			];
		}

		return $options;
	}

	private function getJobTypeOptions($main_category_id) {
		return $this->getSearchOptions($main_category_id, Project::$str_project_type, 'byType');
	}

	private function getJobDurationOptions($main_category_id) {
		return $this->getSearchOptions($main_category_id, Project::$str_project_duration, 'byDuration');
	}

	private function getJobWorkloadOptions($main_category_id) {
		return $this->getSearchOptions($main_category_id, Project::$str_project_workload, 'byWorkload');
	}

	private function getJobExperienceLevelOptions($main_category_id) {
		return $this->getSearchOptions($main_category_id, Project::$str_project_level, 'byExperienceLevel');
	}

	private function getJobPriceOptions($main_category_id, $types) {
		if (!$types)
			return [];

		$options = [];
		$fn_name = null;

		// For fixed job.
		if (!in_array(Project::TYPE_HOURLY, $types)) {
			foreach (Project::$str_project_price as $key => $option) {
				$options[$key] = trans($option[0]);
			}

			$fn_name = 'byPrice';
		// For hourly job
		} elseif (!in_array(Project::TYPE_FIXED, $types)) { 
			$options = Project::$str_project_rate;

			$fn_name = 'byPriceRate';
		}

		return $this->getSearchOptions($main_category_id, $options, $fn_name);
	}

	private function getSortOptions() {
		return [
			'desc' => trans('common.newest'),
			'asc' => trans('common.oldest'),
		];
	}

	private function result(Request $request, $rss = false) {
		$filtered = false;
		$searchParams = [
			'c', 'ac', 'cs', 't', 'el', 'd', 'wl', 'min', 'max', 'q', 'p', 'st'
		];

		$filteredParams = '';
		foreach ( $searchParams as $p ) {
			if ( isset($request->$p) && $request->$p != '' ) {
				$filtered = true;

				if ( $filteredParams ) {
					$filteredParams .= '&';
				}

				$filteredParams .= $p . '=' . $request->$p;
			}
		}

		// Get the settings from config.
		$perPage = Config::get('settings.freelancer.per_page');

		$categoryList = Category::all();

		foreach ($categoryList as $key => $category) {
			$categoryTreeList = $category->byType(Category::TYPE_PROJECT);
			break;
		}

		// Job state
		$state = $request->input('st');

		$jobs = Project::acceptTerm();

		if ( $state == '2' ) {
			$jobs->open();
		} else {
			$jobs->where(function($query) {
				$query->whereRaw('(SELECT COUNT(pa.id) FROM project_applications pa WHERE pa.project_id = projects.id) > 0')
					->orWhere('projects.status', Project::STATUS_OPEN);
			});
		}

		$user = Auth::user();
		if ( !$user || $rss ) {
			$jobs->publics();
		} else {
			$jobs->protected();
		}

		$searchOtherParams = [
			'c', 'ac', 'cs', 't', 'el', 'd', 'wl', 'min', 'max', 'p', 'st'
		];

		$defaultSearched = true;
		foreach ( $searchOtherParams as $p ) {
			if ( isset($_REQUEST[$p]) ) {
				$defaultSearched = false;
			}
		}

		$orders = [1];
		$searchTitle = $request->input('q');
		if ( $searchTitle ) {
			$orders = [];

			$searchTitle = '%' . strtolower(trim($searchTitle)) . '%';
			$filters_by_keyword = [
				'LOWER(projects.subject) LIKE "' . $searchTitle . '"',
				'LOWER(projects.desc) LIKE "' . $searchTitle . '"',
				'LOWER(skills.name) LIKE "' . $searchTitle . '"',
			];

			$jobs->leftJoin('project_skills', 'projects.id', '=', 'project_skills.project_id')
				 ->leftJoin('skills', 'project_skills.skill_id', '=', 'skills.id')
				 ->where(function ($query) use ($filters_by_keyword) {
					foreach ($filters_by_keyword as $filter) {
						$query->orWhereRaw($filter);
					}
				 }
			);

		 	foreach ($filters_by_keyword as $i => $filter) {
				$jobs->addSelect(DB::raw("IF($filter, 50000-$i, 0) AS order$i"));
				$orders[] = "order$i";
			}
		}

		$main_category_id     = $request->input('c');
		$all_check            = $request->input('ac');
		$sub_category_ids     = $request->input('cs') != '' ? explode(',', $request->input('cs')) : [];
		$typeArray 		      = $request->input('t') != '' ? explode(',', $request->input('t')) : [];
		$experienceLevelArray = $request->input('el') != '' ? explode(',', $request->input('el')) : [];
		$priceArray 		  = $request->input('p') != '' ? explode(',', $request->input('p')) : [];
		$durationArray 	      = $request->input('d') != '' ? explode(',', $request->input('d')) : [];
		$workloadArray 	      = $request->input('wl') != '' ? explode(',', $request->input('wl')) : [];
		$bgt_amt_min 		  = $request->input('min', 0);
		$bgt_amt_max 		  = $request->input('max', 50000);

		// By Category
		if ( empty($all_check) && empty($sub_category_ids) && !empty($main_category_id) ) {
			$jobs->where('category_id', -1);
			
		} elseif ( !empty($all_check) && !empty($main_category_id) ) {
			$subCategoryIds = Category::where('parent_id', $main_category_id)
									  ->pluck('id')
									  ->toArray();
			$jobs->whereIn('category_id', $subCategoryIds??[$main_category_id]);
		}

		if ( !empty($sub_category_ids) && empty($all_check) ) {
			$jobs->whereIn('category_id', $sub_category_ids);
		}
		
		// By Job Type
		if ( !empty($typeArray) )
			$jobs->whereIn('projects.type', $typeArray);

		// By Price - it is possible for fixed job.
		if ( !empty($typeArray) && !in_array(Project::TYPE_HOURLY, $typeArray) && $bgt_amt_min !== null && $bgt_amt_max !== null) {
			$price_ranges = Project::pricesWithRange($bgt_amt_min, $bgt_amt_max);
			$jobs->whereIn('projects.price', $price_ranges);
		}

		// By Workload - it is possible for hourly job.
		if ( !empty($typeArray) && !in_array(Project::TYPE_FIXED, $typeArray) && !empty($workloadArray) )
			$jobs->whereIn('workload', $workloadArray);

		// By Experience
		if ( !empty($experienceLevelArray) ) {
			$jobs->whereIn('experience_level', $experienceLevelArray);
		}

		// By Price
		if ( !empty($priceArray) && !empty($typeArray) ) {
			if (!in_array(Project::TYPE_HOURLY, $typeArray))
				$jobs->whereIn('projects.price', $priceArray);
			elseif (!in_array(Project::TYPE_FIXED, $typeArray))
				$jobs->whereIn('affordable_rate', $priceArray);
		}

		// By Duration
		if ( !empty($durationArray) ) {
			$jobs->whereIn('projects.duration', $durationArray);
		}

		$jobs->addSelect(DB::raw('projects.*'));

		$count = count($jobs->get());
    	$sort_order = $request->input('s');

    	if ( $sort_order && !in_array(strtolower($sort_order), ['asc', 'desc']) ) {
    		$sort_order = 'ASC';
    	}
    	
		$jobs = $jobs->orderBy('projects.status', 'DESC')
					->orderByRaw('(' . implode('+', $orders) . ') DESC, projects.created_at ' . $sort_order)
		            ->groupBy('projects.id')
		            ->paginate($perPage);

		if (!$rss)
			$jobs->appends(['c' => $main_category_id, 'sort' => $sort_order]);

		$request->flash();

		view()->share([
			'jobs' 					=> $jobs,
			'categoryTreeList' 		=> $categoryTreeList,
			'jobDurations' 			=> $this->getJobDurationOptions($main_category_id),
			'jobWorkloads' 			=> $this->getJobWorkloadOptions($main_category_id),
			'jobTypes'				=> $this->getJobTypeOptions($main_category_id),
			'sorts'					=> $this->getSortOptions(),
			'jobExperienceLevels'	=> $this->getJobExperienceLevelOptions($main_category_id),
			'job_prices'			=> $this->getJobPriceOptions($main_category_id, $typeArray),
			'resultCount'			=> $count,
			'main_category_id'      => $main_category_id,
			'defaultSearched' 		=> $defaultSearched,
			'filtered' 				=> $filtered,
			'filteredParams' 		=> $filteredParams,
		]);

		return $jobs;
	}

	/**
	* Search the jobs.
	*
	* @param  Request $request
	* @return Response
	*/
	public function job(Request $request) {
		$jobs = $this->result($request);

		if ( !$jobs->items() ) {
			return redirect()->route('search.job', ['page' => $jobs->lastPage()]);
		}

		return view('pages.search.job', [
			'page' => 'search.job'
		]);
	}

	/**
	* Service the jobs via RSS.
	*
	* @param  Request $request
	* @return Response
	*/
	public function rssjob(Request $request) {
		$self = $this;

		$minutes = 1;
		$jobs = Cache::remember('job', $minutes, function() use ($self, $request) {
			return $self->result($request, true);
		});

		$jobs = $self->result($request, true);

		return response()->view('pages.search.rss', [
								'page'             => 'search.rss',
								'last_build_date'  => date('Y-m-d H:i:s'),
								'jobs'             => $jobs,
							])
						 ->header('Content-Type', 'text/xml')
						 ->header('charset', 'UTF-8');
	}

	/**
	* Save the user profile by ajax
	* @author Ro Un Nam
	* @since May 21, 2017
	*/
	public function save_user(Request $request) {
		$user = Auth::user();

		if ( !$user ) {
			abort(404);
		}

		if ( $request->ajax() ) {
			$json = ['success' => false];

			$result = ProfileViewHistory::create([
				'buyer_id' => $user->id,
				'user_id' => $request->input('id'),
			]);

			if ( $result ) {
				$json['success'] = true;
			}

			return response()->json($json);
		} else {
			abort(404);
		}
	}
}