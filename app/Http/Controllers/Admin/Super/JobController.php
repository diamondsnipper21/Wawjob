<?php namespace iJobDesk\Http\Controllers\Admin\Super;
/**
 * @author PYH
 * @since July 9, 2017
 * Job Postings  Page
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use DB;
use Auth;
use Config;
use iJobDesk\Models\User;
use iJobDesk\Models\ActionHistory;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\Notification;
use iJobDesk\Http\Controllers\JobController as Controller;

class JobController extends BaseController {

    private $controller;

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Jobs';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    public function beforeAction(Request $request = null) {
        parent::beforeAction($request);
        
        $this->controller = new Controller();
        $this->controller->beforeAction($request);
    }

    /**
    * Show Job Postings.
    *
    * @return Response
    */
    public function index(Request $request, $user_id = null) {
        $user = null;

        if (!empty($user_id)) {
            $user = ViewUser::find($user_id);

            if (!$user)
                abort(404);
        }

        if (empty($user_id)) {
            add_breadcrumb('Jobs');
        } else {
            add_breadcrumb('Users', route('admin.super.users.list'));
            add_breadcrumb('User', route('admin.super.user.overview', ['user_id' => $user_id]));
            add_breadcrumb('Jobs');
        }

        $action = $request->input('_action');
        $reason = $request->input('_reason');
        // change status

        if ($action == 'CHANGE_STATUS') {

            $status = $request->input('status');
            $ids = $request->input('ids');

            foreach ($ids as $id) {
                $project = Project::find($id);
                $project->status = $status;

                $project->save();
            }

            if ($status == Project::STATUS_SUSPENDED) {
                add_message(sprintf('The %d job(s) have been suspended.', count($ids)), 'success');
                $slug = Notification::JOB_SUSPENDED;
                $this->addNotifications($status, $ids, $slug);
            } else if ($status == Project::STATUS_DELETED) {
                $slug = Notification::JOB_DELETED;
                $this->addNotifications($status, $ids, $slug);

                foreach ($ids as $id) {
                    $project = Project::find($id);
                    $project->delete();
                    $project_applications = ProjectApplication::where('project_applications.project_id', $id)->delete();
                }

                add_message(sprintf('The %d job(s) have been deleted', count($ids)), 'success');
            } else if ($status == Project::STATUS_OPEN) {
                add_message(sprintf('The %d job(s) have been activated', count($ids)), 'success');
                $slug = Notification::JOB_ACTIVATED;
                $this->addNotifications($status, $ids, $slug);
            }
        }

        // sort
        $sort     = $request->input('sort', 'order');
        $sort_dir = $request->input('sort_dir', 'desc');

        $_project = new Project();
        $query_total_proposals = $_project->totalProposalsCount(true);
        $query_total_proposals = str_replace('`project_applications`', 'pa', $query_total_proposals);
        $query_total_proposals = str_replace('from pa', 'from `project_applications` AS pa', $query_total_proposals);
        
        $_project = new Project();
        $query_total_interviews = $_project->totalInterviewsCount(true);
        $query_total_interviews = str_replace('`project_applications`', 'pa', $query_total_interviews);
        $query_total_interviews = str_replace('from pa', 'from `project_applications` AS pa', $query_total_interviews);

        $jobs = Project::leftJoin('project_applications', 'projects.id', '=', 'project_applications.project_id')
                       ->leftJoin('view_users', 'projects.client_id', '=', 'view_users.id')
                       // ->whereIn('project_applications.status',[ProjectApplication::STATUS_NORMAL,ProjectApplication::STATUS_ACTIVE])
                       ->groupBy('projects.id')
                       ->selectRaw('projects.*, view_users.fullname, (' . $query_total_proposals . ') AS total_proposals, (' . $query_total_interviews . ') AS total_interviews, IF(projects.status = ' . Project::STATUS_OPEN . ', 3, IF(projects.status = ' . Project::STATUS_SUSPENDED . ', 2, 1)) AS `order`')
                       ->orderBy($sort, $sort_dir)
                       ->orderBy('projects.created_at', $sort_dir)
                       ->withTrashed();

        if (!empty($user)) {
            $jobs->where('projects.client_id', '=', $user->id);
        }

        $filter = $request->input('filter');

        // By Id
        if (!empty($filter['id'])) {
            $jobs->where('projects.id', $filter['id']);
        }

        // By Title
        if (!empty($filter['title'])) {
            $jobs->where('subject', 'like', '%' . trim($filter['title']) . '%');
        }

        // By Type
        if ( $filter['type'] != '' ) {
            $jobs->where('projects.type', $filter['type']);
        }

        // By Owner
        if (!empty($filter['owner'])) {
            $jobs->where(function($query) use ($filter) {
                $query->orWhereRaw('LOWER(fullname) LIKE "%' . trim(strtolower($filter['owner'])) . '%"')
                      ->orWhere('projects.client_id', '=', $filter['owner']);
            });
        }

        // By Visibility
        if ( $filter['visibility'] != '' ) {
            $jobs->where('is_public', strtolower($filter['visibility']));
        }

        // By Date Posted
        if (!empty($filter['created_at'])) {
            if (!empty($filter['created_at']['from'])) {
                $jobs->where('projects.created_at', '>=', date('Y-m-d H:i:s', strtotime($filter['created_at']['from'])));
            }

            if (!empty($filter['created_at']['to'])) {
                $jobs->where('projects.created_at', '<=', date('Y-m-d H:i:s', strtotime($filter['created_at']['to']) + 24* 3600));
            }
        }

        // By Updated At
        if (!empty($filter['updated_at'])) {
            if (!empty($filter['updated_at']['from'])) {
                $jobs->where('projects.updated_at', '>=', date('Y-m-d H:i:s', strtotime($filter['updated_at']['from'])));
            }

            if (!empty($filter['updated_at']['to'])) {
                $jobs->where('projects.updated_at', '<=', date('Y-m-d H:i:s', strtotime($filter['updated_at']['to']) + 24* 3600));
            }
        }

        // By Status
        if ( $filter['status'] != '' ) {
            $jobs->where('projects.status', strtolower($filter['status']));
        }

        $request->flash();

        return view('pages.admin.super.job.jobs', [
            'page' => 'super.'.(!empty($user_id)?'user.buyer.':'').'job.jobs',
            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir,
            'jobs' => $jobs->paginate($this->per_page),
            'user' => $user
        ]);
    }

    public function overview(Request $request, $id, $user_id = null) {
        if (empty($user_id)) {
            add_breadcrumb('Jobs', route('admin.super.job.jobs'));
            add_breadcrumb('Overview');
        } else {
            add_breadcrumb('Users', route('admin.super.users.list'));
            add_breadcrumb('User', route('admin.super.user.overview', ['user_id' => $user_id]));
            add_breadcrumb('Job Postings', route('admin.super.user.buyer.jobs', ['user_id' => $user_id]));
            add_breadcrumb('Overview');
        }

        $user = null;
        if (!empty($user_id))
            $user = ViewUser::find($user_id);

        view()->share([
            'page_title' => $this->page_title,
            'user' => $user,
            'user_id' => $user_id
        ]);

        return $this->controller->overview($request, $id, $user_id);
    }

    public function invitation(Request $request, $id, $user_id = null) {

        if (empty($user_id)) {
            add_breadcrumb('Jobs', route('admin.super.job.jobs'));
            add_breadcrumb('Invitations');
        } else {
            add_breadcrumb('Users', route('admin.super.users.list'));
            add_breadcrumb('User', route('admin.super.user.overview', ['user_id' => $user_id]));
            add_breadcrumb('Job Postings', route('admin.super.user.buyer.jobs', ['user_id' => $user_id]));
            add_breadcrumb('Invitations');
        }

        $user = null;
        if (!empty($user_id))
            $user = ViewUser::find($user_id);

        view()->share([
            'page_title' => $this->page_title,
            'user' => $user,
            'user_id' => $user_id
        ]);

        return $this->controller->invite_freelancers($request, $id, 'invited', $user_id);
    }

    public function proposal(Request $request, $id, $page = '', $user_id = '') {
        if (empty($user_id)) {
            add_breadcrumb('Jobs', route('admin.super.job.jobs'));
            add_breadcrumb('Proposals');
        } else {
            add_breadcrumb('Users', route('admin.super.users.list'));
            add_breadcrumb('User', route('admin.super.user.overview', ['user_id' => $user_id]));
            add_breadcrumb('Job Postings', route('admin.super.user.buyer.jobs', ['user_id' => $user_id]));
            add_breadcrumb('Proposals');
        }

        $user = null;
        if (!empty($user_id))
            $user = ViewUser::find($user_id);

        view()->share([
            'page_title' => $this->page_title,
            'user' => $user,
            'user_id' => $user_id
        ]);

        return $this->controller->review_proposals($request, $id, $page, $user_id);
    }

    public function interview(Request $request, $id, $page = '', $user_id = null) {
        if (empty($user_id)) {
            add_breadcrumb('Jobs', route('admin.super.job.jobs'));
            add_breadcrumb('Interviews');
        } else {
            add_breadcrumb('Users', route('admin.super.users.list'));
            add_breadcrumb('User', route('admin.super.user.overview', ['user_id' => $user_id]));
            add_breadcrumb('Job Postings', route('admin.super.user.buyer.jobs', ['user_id' => $user_id]));
            add_breadcrumb('Interviews');
        }

        $user = null;
        if (!empty($user_id))
            $user = ViewUser::find($user_id);

        view()->share([
            'page_title' => $this->page_title,
            'user' => $user,
            'user_id' => $user_id
        ]);

        return $this->controller->interviews($request, $id, $page, $user_id);
    }

    public function hire_offers(Request $request, $id, $user_id = null) {
        if (empty($user_id)) {
            add_breadcrumb('Jobs', route('admin.super.job.jobs'));
            add_breadcrumb('Interviews');
        } else {
            add_breadcrumb('Users', route('admin.super.users.list'));
            add_breadcrumb('User', route('admin.super.user.overview', ['user_id' => $user_id]));
            add_breadcrumb('Job Postings', route('admin.super.user.buyer.jobs', ['user_id' => $user_id]));
            add_breadcrumb('Interviews');
        }

        $user = null;
        if (!empty($user_id))
            $user = ViewUser::find($user_id);

        view()->share([
            'page_title' => $this->page_title,
            'user' => $user,
            'user_id' => $user_id
        ]);

        return $this->controller->hire_offers($request, $id, $user_id);
    }

    public function action_history(Request $request, $id, $user_id = null) {
        if (empty($user_id)) {
            add_breadcrumb('Jobs', route('admin.super.job.jobs'));
            add_breadcrumb('Action History');
        } else {
            add_breadcrumb('Users', route('admin.super.users.list'));
            add_breadcrumb('User', route('admin.super.user.overview', ['user_id' => $user_id]));
            add_breadcrumb('Job Postings', route('admin.super.user.buyer.jobs', ['user_id' => $user_id]));
            add_breadcrumb('Action History');
        }

        $user = null;
        if (!empty($user_id))
            $user = ViewUser::find($user_id);

        try {

            $sort     = $request->input('sort', 'action_histories.created_at');
            $sort_dir = $request->input('sort_dir', 'desc');

            $histories = ActionHistory::addSelect('action_histories.*')
                                      ->addSelect('du.fullname AS doer_fullname')
                                      ->addSelect('du.id AS doer_id')
                                      ->join('view_users AS du', 'du.id', '=', 'action_histories.doer_id')
                                      ->join('projects AS p', 'p.id', '=', 'action_histories.target_id')
                                      ->where('p.id', $id)
                                      ->where('action_histories.type', ActionHistory::TYPE_JOB)
                                      ->orderBy($sort, $sort_dir);

            // Filtering
            $filter = $request->input('filter');

            // By Action Type
            if (!empty($filter['type'])) {
                $histories->whereRaw("action_type LIKE '%" . trim($filter['type']) . "%'");
            }

            // By Message
            if (!empty($filter['description'])) {
                $histories->whereRaw("action_histories.reason LIKE '%" . trim($filter['description']) . "%'");
            }

            // By Doer
            if (!empty($filter['doer_fullname'])) {
                $histories->whereRaw("du.fullname LIKE '%" . trim($filter['doer_fullname']) . "%'");
            }

            // By Create At
            if (!empty($filter['created_at'])) {
                if (!empty($filter['created_at']['from'])) {
                    $histories->where('action_histories.created_at', '>=', date('Y-m-d H:i:s', strtotime($filter['created_at']['from'])));
                }

                if (!empty($filter['created_at']['to'])) {
                    $histories->where('action_histories.created_at', '<=', date('Y-m-d H:i:s', strtotime($filter['created_at']['to']) + 24* 3600));
                }
            }

            $request->flashOnly('filter');

            $job = Project::findOrFail($id);
                                        
            return view('pages.admin.super.job.action_history', [
                'page'          => 'super.'.(!empty($user_id)?'user.buyer.':'').'job.action_history',
                'job'           => $job,
                'user'          => $user,
                'user_id'       => $user_id,
                'sort'          => $sort,
                'sort_dir'      => '_' . $sort_dir,
                'histories'     => $histories->paginate($this->per_page)
            ]); 
            
        } catch(Exception $e) {
            return redirect()->route('admin.super.job.overview', ['id' => $id]);
        }
    }

    // add notification to buyer
    private function addNotifications($status, $ids, $slug) {

        foreach ($ids as $key => $id) {
            $project = Project::find($id);

            Notification::send(
                $slug,
                SUPERADMIN_ID, 
                $project->client_id,
                [
                    'project' => $project->subject
                ]
            );
        }
        
        return '';
    }
    
}