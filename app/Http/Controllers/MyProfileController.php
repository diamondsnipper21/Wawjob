<?php 
namespace iJobDesk\Http\Controllers;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Auth;
use Config;
use DB;
use Exception;
use Validator;

// Models
use iJobDesk\Models\User;
use iJobDesk\Models\UserLanguage;
use iJobDesk\Models\UserSkill;
use iJobDesk\Models\UserProfile;
use iJobDesk\Models\UserPortfolio;

use iJobDesk\Models\Language;
use iJobDesk\Models\Skill;

use iJobDesk\Models\File;

class MyProfileController extends Controller {

    /**
     * user/my-profile-dev
     *
     * @author KCG
     * @since Feb 1, 2018
     * @param  Request $request
     * @return Response
    */
    public function index(Request $request) {
        $user = Auth::user();

        $profile = $user->profile;

        $action = $request->input('_action', '');

        if ($action == 'SAVE' && $request->isMethod('post')) {
            // if ($user->isSuspended())
            //     abort(404);

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

                if ($user_profile->save()) {
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

                    // Call User Observer for saving avatar
                    $user->save();

                    add_message(trans('profile.update_profile_success'), 'success');

                } else {
                    add_message(trans('profile.update_profile_error'), 'danger');
                }
            }
        }

        $user = User::find($user->id);

        $portfolios = UserPortfolio::where('user_id', $user->id);

        return view('pages.freelancer.user.my_profile', [
            'page'      => 'freelancer.user.my_profile',
            'user'      => $user,
            'portfolios'=> $portfolios->paginate(8),
            'j_trans'   => [
                'select_languages'          => trans('profile.select_languages'),
                'confirm_removing_avatar'   => trans('profile.confirm_removing_avatar'),

                'delete_confirm_portfolio'          => trans('profile.delete_confirm_portfolio'),
                'delete_confirm_certification'      => trans('profile.delete_confirm_certification'),
                'delete_confirm_employment'         => trans('profile.delete_confirm_employment'),
                'delete_confirm_education'          => trans('profile.delete_confirm_education'),
                'delete_confirm_experience'         => trans('profile.delete_confirm_experience')
            ],

            'config' => Config::get('settings'),
        ]);
    }

    /**
     * my-profile/add
     */
    public function add(Request $request) {
        $user = Auth::user();

        if ( $request->isMethod('post') ) {
            if ($user->isSuspended()) {
                return redirect()->route('profile.step', ['step' => $user->profile_step]);
            }
        }

        $success = true;

        $var_name               = $request->input('var_name');
        $collection_var_name    = $request->input('collection_var_name');
        $id                     = $request->input('id');

        $class = 'iJobDesk\\Models\\User' . ucfirst($var_name); // iJobDesk\Models\UserExperience
        $item = new $class();
        if ($id)
            $item = $class::find($id);

        // Get validation rules
        eval('$rules = ' .  $class . '::getValidator();');
        $validator = Validator::make($request->all(), $rules);

        if ( $validator->fails() ) {
            $errors = $validator->messages();
            if ( $errors->all() ) {
                foreach ( $errors->all() as $error )
                    add_message($error, 'danger');
            }

            $success = false;
        } else {
            $data = $request->input('profile.'.$var_name);
            foreach ($data as $key => $value) {
                $item->$key = $value;
            }
            $item->user_id = $user->id;

            // Employment History
            if ($var_name == 'employment') {
                if (!empty($data['to_present'])) {
                    $item->to_year  = date('Y');
                    $item->to_month = date('m');
                } else {
                    $item->to_present  = 0;
                }
            }
            
            if ($item->save()) {
                if (!$id) {

                    // Update user points
                    switch ( $var_name ) {
                        case 'employment':
                            $user->point->updateEmploymentHistory();
                            break;
                        case 'education':
                            $user->point->updateEducation();
                            break;
                        case 'certification':
                            $user->point->updateCertification();
                            break;
                        case 'portfolio':
                            $user->point->updatePortfolio();
                            break;
                        default:
                            break;
                    }

                    add_message(trans('profile.create_success_' . strtolower($var_name), ['name' => trans('profile.'.strtolower($var_name))]), 'success');
                } else {
                    add_message(trans('profile.update_success_' . strtolower($var_name), ['name' => trans('profile.'.strtolower($var_name))]), 'success');
                }
            }
        }

        return [
                'collection' => $user->$collection_var_name,
                'alerts' => show_messages(true),
                'success' => $success,
        ];
    }

    /**
     * my-profile/remove
     */
    public function delete(Request $request) {
        $user = Auth::user();

        if ($user->isSuspended())
            abort(404);

        $var_name               = $request->input('var_name');
        $collection_var_name    = $request->input('collection_var_name');
        $id                     = $request->input('id');

        $class = 'iJobDesk\\Models\\User' . ucfirst($var_name); // iJobDesk\UserExperience
        $item = new $class();
        
        $item = $class::find($id);
        $item->delete();

        add_message(trans('profile.delete_success_' . strtolower($var_name), ['name' => trans('profile.'.strtolower($var_name))]), 'success');

        return [
                'collection'    => $user->$collection_var_name,
                'alerts'        => show_messages(true),
                'success'       => true,
        ];
    }

    /**
     * @param $request
     * Remove avatar image
     */
    public function remove_avatar(Request $request) {
        $user = Auth::user();

        // if ($user->isSuspended())
        //     abort(404);

        $file = File::getAvatar($user->id);
        if ($file)
            $file->delete();

        add_message(trans('profile.remove_avatar_success'), 'success');

        return [
            'alerts' => show_messages(true),
            'url'   => avatar_url($user)       
        ];
    }

    /**
     * @param $request
     * /profile-settings page
     */
    public function profile_settings(Request $request) {
        $user = Auth::user();

        if ( $request->isMethod('post') ) {

            if ($user->isSuspended())
                abort(404);

            $user_profile = $user->profile;
            $user_profile->share = intval($request->input('profile_share'));
            $user_profile->hide_earning = intval($request->input('profile_hide_earning'));

            if ( $user_profile->save() ) {
                add_message(trans('profile.update_profile_success'), 'success');
            } else {
                add_message(trans('profile.update_profile_error'), 'danger');
            }
        }

        return view('pages.freelancer.user.profile_settings', [
            'page' => 'freelancer.user.profile_settings',
            'user' => $user,
        ]);
    }
}