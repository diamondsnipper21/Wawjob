<?php namespace iJobDesk\Http\Controllers;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Auth;
use Storage;
use Config;
use Session;
use Exception;

// Models
use iJobDesk\Models\User;
use iJobDesk\Models\Project;
use iJobDesk\Models\UserSavedProject;

class UserSavedProjectController extends Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index(Request $request) {
		$perPage = Config::get('settings.freelancer.per_page');
		$user = Auth::user();

		$columnName = $request->input('sortCode', 'posted_at');

		$userSavedJobs = UserSavedProject::leftJoin('projects', 'user_saved_projects.project_id', '=', 'projects.id')
										->where('user_saved_projects.user_id', $user->id)
										->whereIn('projects.is_public', [
											Project::STATUS_PUBLIC,
											Project::STATUS_PROTECTED,
										]);
		
		if (empty($columnName)) {
			$userSavedJobs = $userSavedJobs->orderBy('user_saved_projects.posted_at','desc');
		} elseif ($columnName == 'created_at') {
			$userSavedJobs = $userSavedJobs->orderBy('user_saved_projects.created_at','desc');
		} elseif ($columnName == 'subject') {
			$userSavedJobs = $userSavedJobs->orderBy('projects.subject', 'asc');
		} elseif ($columnName == 'posted_at') {
			$userSavedJobs = $userSavedJobs->orderBy('user_saved_projects.posted_at','desc');
		}		

		return view('pages.freelancer.job.saved_jobs', [
			'page'          	=> 'freelancer.job.saved_jobs',
			'userSavedJobs' 	=> $userSavedJobs->paginate($perPage),
			'per_page' 			=> $perPage,
			'colValue'			=> $columnName,
		]);    
	}

	public function create(Request $request, $id)
	{		
		$user = Auth::user();    

		$savedProject = new UserSavedProject;

		$job = Project::find($id);
		if ( $job ) {
			$savedProject->project_id = $id;
			$savedProject->user_id = $user->id;
			$savedProject->posted_at = $job->created_at;

			$savedProject->save();
		}

		if ( $request->ajax() ) {
			return response()->json([
		        'success'   => true
		    ]);
		}

		return redirect()->route('saved_jobs.index');
	}

	public function destroy(Request $request, $id)
	{
		UserSavedProject::where('project_id', $id)->delete();

		return redirect()->route('saved_jobs.index');
	}

}