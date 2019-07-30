<?php 
namespace iJobDesk\Http\Controllers\Frontend\User;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Auth;
use Config;
use Cookie;
use DB;
use Session;
use Storage;
use Exception;
use Validator;

use iJobDesk\Models\SecurityQuestion;
use iJobDesk\Models\User;
use iJobDesk\Models\UserCompany;
use iJobDesk\Models\UserLanguage;
use iJobDesk\Models\UserPortfolio;
use iJobDesk\Models\UserProfile;
use iJobDesk\Models\UserSecurityQuestion;
use iJobDesk\Models\UserSkill;

use iJobDesk\Models\Contract;
use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\ProjectInvitation;
use iJobDesk\Models\ProfileViewHistory;

use iJobDesk\Models\Category;
use iJobDesk\Models\Language;
use iJobDesk\Models\Skill;

// ViewModels
use iJobDesk\Models\Views\ViewUser;

class ProfileController extends Controller {

    /**
    * Constructor
    */

    public static $profile_setup_steps = [
        3 => ['portfolios', 'portfolio'],
        4 => ['certifications', 'certification'],
        5 => ['employments', 'employment'],
        6 => ['educations', 'education'],
        7 => ['experiences', 'experience']
    ];

    /**
    * Show user proifle, it depends to the user's setting to show,
    * refrence to share field on user_profiles table.
    * Note that allow only freelancers.
    *
    * @param  integer $user_id User id
    * @return Response
    */
    public function index(Request $request, $user_id = 0) {
        $current_user = Auth::user();

        if ( !$user_id ) {
            abort(404);
        }

        $user = null;
        if ($current_user && $current_user->isAdmin())
            $user = ViewUser::find($user_id);
        else {
            $user = ViewUser::findByUnique($user_id);
            $user_id = $user->id;
        }

        if ( !$user ) {
            abort(404);
        }

        if ( $user->isBuyer() ) {
            abort(404);
        }

        if ( $user->trashed() ) {
            abort(404);
        }

        if ( $user->isSuspended() ) {
            abort(404);
        }

        if ( !$current_user && in_array($user->profile->share, [1, 2]) ) {
            abort(404);
        }

        if ($current_user && $user_id != $current_user->id) {
            $can_view_profile = false;
            if ($current_user->isBuyer() && $user->profile->share == 2) {
                // Check if this freelancer sent proposal on project of this buyer(user) or not
                if (ProjectApplication::join('projects', 'projects.id', '=', 'project_id')
                                      ->where('user_id', $user->id)
                                      ->where('projects.client_id', $current_user->id)
                                      ->exists())
                    $can_view_profile = true;
                // Check if this freelancer received invitation on project of this buyer(user) or not
                elseif (ProjectInvitation::where('sender_id', $current_user->id)
                                         ->where('receiver_id', $user->id)
                                         ->exists())
                    $can_view_profile = true;
                // Check if this freelancer received offers on project of this buyer(user) or not
                elseif (ProjectOffer::where('sender_id', $current_user->id)
                                    ->where('receiver_id', $user->id)
                                    ->exists())
                    $can_view_profile = true;
                // Check if this freelancer have been working on contract of this buyer(user) or not
                elseif (Contract::where('buyer_id', $current_user->id)
                                ->where('contractor_id', $user->id)
                                ->exists())
                    $can_view_profile = true;
            } elseif ($user->profile->share == 2) {
                $can_view_profile = false;
            } else {
                $can_view_profile = true;
            }

            if (!$can_view_profile)
                abort(404);
        }

        $perPage = Config::get('settings.freelancer.per_page');

        // Work history feedback
        $feedback_sort_by = $request->input('feedback_sort_by', 'newest');
        $contracts = Contract::getContracts([
            'contractor_id' => $user->id,
            'status' => [
                Contract::STATUS_OPEN,
                Contract::STATUS_CLOSED,
            ],
            'orderby' => $feedback_sort_by,
            'earned' => true,
            'paginate' => true
        ]);

        // Portfolios
        $portfolio_category = $request->input('portfolio_category');
        $portfolios = UserPortfolio::where('user_id', $user->id);
        if ($portfolio_category)
            $portfolios->where('cat_id', $portfolio_category);

        $user->sc = $user->totalScore();
        $categories = UserPortfolio::getCategories($user->id);
        $focus_user = ViewUser::find($user->id);

        $jobs = [];
        if ( $current_user && $current_user->isBuyer() ) {
            $jobs = Project::where('client_id', $current_user->id)
                            ->where('status', Project::STATUS_OPEN)
                            ->orderBy('subject')
                            ->get();
        }

        // already hired or already invited handler
        $filtered_jobs = [];
        foreach ($jobs as $key => $job) {
            $already_hired_user_ids = Contract::where('project_id', $job->id)
                                                ->where('status', '<>', Contract::STATUS_CLOSED)
                                                ->pluck('contractor_id')
                                                ->toArray();

            $already_invited_user_ids = ProjectInvitation::where('project_id', $job->id)
                                                        ->pluck('receiver_id')
                                                        ->toArray();

            if ( !in_array($user_id, $already_hired_user_ids) && !in_array($user_id, $already_invited_user_ids) ) {
                $filtered_jobs[] = $job;
            }
        }

        return view('pages.freelancer.user.profile', [
            'page' => ( $current_user && $current_user->isAdmin() ? 'super.user.freelancer.profile' : 'freelancer.user.profile'),
            'user'              => $user,
            'english_levels'    => Category::getEnLevels(),

            'portfolioes'       => $portfolios->paginate(8),
            'portfolio_category'=> $portfolio_category,

            'contracts'         => $contracts->paginate($perPage),
            'feedback_sort_by'  => $feedback_sort_by,

            'categories'        => $categories,
            'focus_user'        => $focus_user,
            'isSaved'           => ($current_user ? ProfileViewHistory::isSaved($current_user->id, $user_id) : false),
            'jobs'              => $filtered_jobs,
            'j_trans'           => [
                'saved' => trans('common.saved'),
            ],
        ]);
    }

    public function start(Request $request) {
        $user = Auth::user();
        
        if ( $request->isMethod('post') ) {
            $user->profile_step = 1;
            if ( $user->save() ) {
                return redirect()->route('profile.step', ['step' => 1]);
            }
        }

        return view('pages.freelancer.step.start', [
            'page' => 'freelancer.step.start',
            'step' => 0
        ]);
    }

    public function security_question(Request $request) {
        $user = Auth::user();

        // Get all security questions
        $securityQuestions = SecurityQuestion::where('is_active', 1)
            ->whereNull('deleted_at')
            ->orderby('question', 'asc')
            ->select(['id', 'question', 'category_id'])->get();

        // If user input info
        if ( $request->isMethod('post') ) {
            $userSecurityQuestion = new UserSecurityQuestion;
            $userSecurityQuestion->user_id = $user->id;
            $userSecurityQuestion->question_id = $request->input('question_id');
            $userSecurityQuestion->answer = md5($request->input('answer'));
            if ( $userSecurityQuestion->save() ) {
                Session::put('user_secured', $user->id);

                $user->profile_step = 2;
                if ( $user->save() ) {
	                $user->updateLastActivity();

		            if ( $request->input('remember') ) {
		                $minutes = 14 * 24 * 60; // for 2 weeks = 14 days
                        Cookie::forget('remember_answer');
                        $remember_answer = cookie('remember_answer', $request->input('answer'), $minutes);

		                return redirect()->route('profile.step', ['step' => 2])->withCookie($remember_answer);
		            } else {
		            	return redirect()->route('profile.step', ['step' => 2]);
		            }
		        }
	        }
        }

        return view('pages.freelancer.step.security_question', [
            'page' => 'freelancer.step.security_question',
            'security_questions' => $securityQuestions,
            'step' => 1
        ]);
    }

	public function about_me(Request $request) {
	    $user = Auth::user();
	    
	    if ( $request->isMethod('post') ) {

            $profile = $request->input('profile');
            $languages  = $request->input('profile.languages', []);
            $skills     = $request->input('profile.skills', []);

            $validator = Validator::make($request->all(), [
                'profile.title'      => 'required|max:50',
                'profile.rate'       => 'required|numeric|min:0.5|max:999.99',
                'profile.available'  => [
                    'required',
                    Rule::in(array_keys(UserProfile::availabilities())),
                ],
                'profile.share'      => [
                    'required',
                    Rule::in(array_keys(UserProfile::visibilities())),
                ],
                'profile.desc'       => 'required|max:5000',
                'profile.skills'     => 'max:10',
                'profile.languages'  => 'max:5'
            ]);

            if ( $validator->fails() ) {
                $errors = $validator->messages();
                if ( $errors->all() ) {
                    foreach ( $errors->all() as $error )
                        add_message($error, 'danger');
                }
            } else {
                $user_profile = $user->profile;
                foreach ($profile as $attribute => $value) {
                    if ($attribute == 'languages' || $attribute == 'skills')
                        continue;

                    $user_profile->$attribute = $value;
                }

                if ( $user_profile->save() ) {
                    // Update languages
                    foreach ($languages as $language) {
                        if ($user->languages->contains('id', $language))
                            continue;

                        if (!Language::find($language))
                            continue;

                        $user_language = new UserLanguage();
                        $user_language->user_id = $user->id;
                        $user_language->lang_id = $language;

                        $user_language->save();
                    }

                    // Remove other languages.
                    foreach ($user->languages as $language) {
                        if (in_array($language->id, $languages))
                            continue;

                        UserLanguage::where('user_id', $user->id)
                                    ->where('lang_id', $language->id)
                                    ->delete();
                    }

                    // Update skills
                    foreach ($skills as $skill) {
                        if ($user->skills->contains('id', $skill))
                            continue;

                        if (!Skill::find($skill))
                            continue;

                        $user_skill = new UserSkill();
                        $user_skill->user_id  = $user->id;
                        $user_skill->skill_id = $skill;

                        $user_skill->save();
                    }

                    // Remove other skills.
                    foreach ($user->skills as $skill) {
                        if (in_array($skill->id, $skills))
                            continue;

                        UserSkill::where('skill_id', $skill->id)
                                 ->where('user_id', $user->id)
                                 ->delete();
                    }

                    $user->profile_step = 3;
                    if ( $user->save() ) {
                        $user->updateLastActivity();

                        return redirect()->route('profile.step', ['step' => 3]);
                    }
                }
            }
	    }

	    return view('pages.freelancer.step.about_me', [
	        'page' => 'freelancer.step.about_me',
	        'user' => $user,
            'step' => 2
	    ]);
	}

    public function add_item(Request $request, $step = 0) {
        $user = Auth::user();

        view()->share([
            'j_trans'   => [
                'select_languages'          => trans('profile.select_languages'),
                'confirm_removing_avatar'   => trans('profile.confirm_removing_avatar'),

                'delete_confirm_portfolio'          => trans('profile.delete_confirm_portfolio'),
                'delete_confirm_certification'      => trans('profile.delete_confirm_certification'),
                'delete_confirm_employment'         => trans('profile.delete_confirm_employment'),
                'delete_confirm_education'          => trans('profile.delete_confirm_education'),
                'delete_confirm_experience'         => trans('profile.delete_confirm_experience')
            ]
        ]);

        if ( $request->isMethod('post') ) {
            if ($user->isSuspended()) {
                return redirect()->route('profile.step', ['step' => $user->profile_step]);
            }
        }

        if ($step == 0)
            return redirect()->route('profile.start');
        elseif ($step == 1) // security question page
            return $this->security_question($request);
        elseif ($step == 2) // about me
            return $this->about_me($request);

        if ($request->isMethod('POST') && $request->input('_action') != 'refresh') {
            if ($request->input('_action') == 'back') {
                $user->profile_step = $this->get_step_num($request) - 1;
            } else {
                $user->profile_step = $this->get_step_num($request) + 1;
            }

            if ( $user->save() ) {
                $user->updateLastActivity();
            }

            if ($user->profile_step == 8) {
                session(['show_congratulation' => 1]);
                return redirect()->route('user.dashboard');
            }

            return redirect()->route('profile.step', ['step' => $user->profile_step]);
        }

        $step_info = self::$profile_setup_steps[$step];

        $this->setPageTitle(trans('page.freelancer.step.add_'.$step_info[1].'.title'));

        return view('pages.freelancer.step.add_item', [
            'page' => 'freelancer.step.add_item',
            'collection_var_name' => $step_info[0],
            'var_name' => $step_info[1],
            'step' => $step
        ]);
    }

    private function get_step_num($request) {
        $collection_var_name = $request->input('collection_var_name');
        $var_name = $request->input('var_name');

        foreach (self::$profile_setup_steps as $i => $step) {
            if ($step[0] == $collection_var_name && $step[1] == $var_name)
                return $i;
        }

        return $i;
    }

    public function add(Request $request) {
        $controller = new \iJobDesk\Http\Controllers\MyProfileController();
        $result = $controller->add($request);

        return $result;
    }

    public function delete(Request $request) {
        $controller = new \iJobDesk\Http\Controllers\MyProfileController();
        $result = $controller->delete($request);

        return $result;
    }
}