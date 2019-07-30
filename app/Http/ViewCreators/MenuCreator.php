<?php namespace iJobDesk\Http\ViewCreators;

use Illuminate\Contracts\View\View;
use Illuminate\Users\Repository as UserRepository;

use Config;
use Route;

use Auth;

use iJobDesk\Models\User;
use iJobDesk\Models\ProjectMessageThread;
use iJobDesk\Models\ProjectMessage;

class MenuCreator
{

    /**
    * Bind data to the view.
    *
    * @param  View  $view
    * @return void
    */
    public static function create() {
        self::addRightMenu();
        self::addLangMenu();
        self::addUserSettingsMenu();
        self::addReportMenu();
        self::addMainMenu();
        self::addAboutMenu();
    }

    /**
    * User Right Menu.
    *
    * @author nada
    * @since Jan 22, 2015
    * @return void
    */
    protected static function addRightMenu() {
        $user = Auth::user();
        $right_menu = false;

        $conf = Config::get('menu');

        if ($user) {
            if ($user->isFreelancer()) {
                $right_menu = $conf['freelancer_right_menu'];
            }
            else if ($user->isBuyer()) {
                $right_menu = $conf['buyer_right_menu'];
            }
        }

        if ($right_menu) {

            $start = true;
            foreach ( $right_menu as $key => &$root) {
                $root['pos'] = false;

                if ($start) {
                    $root['pos'] = 'start';
                    $start = false;
                }

                if (isset($root['route'])) {

                } else {
                    $root['route'] = false;
                }

                if (!isset($root['icon'])) {
                    $root['icon'] = false;
                }
            }
            $root['pos'] = 'last';
        }

        view()->share('right_menu', $right_menu);
    }

    /**
    * User lang Menu.
    *
    * @author sg
    * @since Jan 22, 2017
    * @return void
    */
    protected static function addLangMenu()
    {
        $user = Auth::user();
        $lang_menu = false;

        $conf = Config::get('menu');

        if ($user) {
            $lang_menu = $conf['lang_menu'];
        }

        if ($lang_menu) {

            $start = true;
            foreach ( $lang_menu as $key => &$root) {
                $root['pos'] = false;

                if ($start) {
                    $root['pos'] = 'start';
                    $start = false;
                }

                if (isset($root['route'])) {

                } else {
                    $root['route'] = false;
                }

                if (!isset($root['icon'])) {
                    $root['icon'] = false;
                }
            }
            $root['pos'] = 'last';
        }

        view()->share('lang_menu', $lang_menu);
    }

    protected static function addUserSettingsMenu()
    {
        $user = Auth::user();
        $user_settings_menu = false;
        $route = Route::currentRouteName();
        $conf = Config::get('menu');

        if ($user) {
            if ($user->isFreelancer()) {
                $user_settings_menu = $conf['freelancer_user_settings_menu'];
            }
            else if ($user->isBuyer()) {
                $user_settings_menu = $conf['buyer_user_settings_menu'];
            }
        }

        if ($user_settings_menu) {
            $start = true;
            foreach ( $user_settings_menu as $key => &$root) {
                if (strpos($key, 'SEPERATOR') !== FALSE)
                    continue;

                $root['pos'] = false;
                $root['active'] = false;

                if ($start) {
                    $root['pos'] = 'start';
                    $start = false;
                }

                if (isset($root['route'])) {
                    if ($root['route'] == $route) {
                        $root['active'] = true;
                    }
                } else {
                    $root['route'] = false;
                }

                if (!isset($root['icon'])) {
                    $root['icon'] = false;
                }
            }
            $root['pos'] = 'last';
        }

        view()->share('user_settings_menu', $user_settings_menu);
    }

    protected static function addReportMenu()
    {
        $user = Auth::user();
        $report_sidebar_menu = false;
        $route = Route::currentRouteName();
        $conf = Config::get('menu');

        if ($user) {
            if ($user->isFreelancer()) {
                $report_sidebar_menu = $conf['freelancer_report_menu'];
            }
            else if ($user->isBuyer()) {
                $report_sidebar_menu = $conf['buyer_report_menu'];
            }
        }

        if ($report_sidebar_menu) {
            $start = true;
            foreach ( $report_sidebar_menu as $key => &$root) {
                $root['pos'] = false;
                $root['active'] = false;

                if ($start) {
                    $root['pos'] = 'start';
                    $start = false;
                }

                if (isset($root['route'])) {
                    if ($root['route'] == $route) {
                        $root['active'] = true;
                    }
                } else {
                    $root['route'] = false;
                }

                if (!isset($root['icon'])) {
                    $root['icon'] = false;
                }
            }
            $root['pos'] = 'last';
        }

        view()->share('report_sidebar_menu', $report_sidebar_menu);
    }

    protected static function addMainMenu() {
        $conf = Config::get('menu');
        $route = Route::currentRouteName();

        $user = Auth::user();
        $main_menu = false;
        $main_sub_menu = false;

        $start = true;

        if ($user) {
            if ($user->isFreelancer()) {
                $main_menu = $conf['freelancer_main_menu'];
            }
            else if ($user->isBuyer()) {
                $main_menu = $conf['buyer_main_menu'];
            }
        }

        if ($main_menu) {
            foreach ($main_menu as $key => &$root) {
                $has_children = isset($root['children']);
                $root['pos'] = false;
                $root['active'] = false;

                if ($start) {
                    $root['pos'] = 'start';
                    $start = false;
                }

                if (isset($root['route'])) {
                    if ($root['route'] == $route) {
                        $root['active'] = true;
                    }
                } else {
                    $root['route'] = false;
                }

                if ($has_children) {
                    $c_start = true;
                    foreach ($root['children'] as $child_key => &$child) {
                        $child['pos'] = false;
                        if ($c_start) {
                            $child['pos'] = 'start';
                            $c_start = false;
                        }

                        $child['active'] = false;
                        if (isset($child['route'])) {
                            if ($child['route'] == $route) {
                                $child['active'] = true;
                                $root['active'] = true;
                            }
                        } else {
                            $child['route'] = false;
                        }

                        // Search for active menu item in 3-level tree
                        if (isset($child['children'])) {
                            foreach ($child['children'] as $child_key => &$child2) {
                                $child2['active'] = false;
                                if (isset($child2['route'])) {
                                    if ($child2['route'] == $route) {
                                        $child2['active'] = true;
                                        $child['active'] = true;
                                        $root['active'] = true;
                                    }
                                } else {
                                    $child2['route'] = false;
                                }
                            }
                        }

                        // Hidden Menu Item
                        if ( isset($child['hidden']) ) {
                            unset($root['children'][$child_key]);
                        }

                        // Restricted from suspended user
                        if ( $user->isSuspended() && isset($child['restricted']) ) {
                        	unset($root['children'][$child_key]);
                        }
                    }
                    $child['pos'] = 'last';

                    if ($root['active']) {
                        $main_sub_menu = array('root_key'=>$key, 'sub_menu'=>$root['children']);
                    }
                } else {
                    $root['children'] = false;
                }
            }
            $root['pos'] = 'last';
        }

        //add by so gwang for unread-message-notification 
        if (isset($user)) {
            $unread_msg_count = ProjectMessage::join('project_message_threads AS pmt', 'pmt.id', '=', 'project_messages.thread_id')
                                              ->join('project_applications AS pa', 'pmt.application_id', '=', 'pa.id')
                                              ->join('projects AS p', 'pa.project_id', '=', 'p.id')
                                              ->where(function($query) use ($user) {
                                                    $query->where('pmt.sender_id', $user->id)
                                                          ->orWhere('pmt.receiver_id', $user->id);
                                              })
                                              ->where('project_messages.sender_id', '<>', $user->id)
                                              ->whereRaw("(reader_ids NOT LIKE '%[$user->id]%' OR reader_ids IS NULL)")
                                              ->whereNull('p.deleted_at')
                                              ->whereNull('pa.deleted_at')
                                              ->whereNull('project_messages.deleted_at')
                                              ->count();

            view()->share('unread_msg_count', $unread_msg_count);
        }

        view()->share('main_menu', $main_menu);
        view()->share('main_sub_menu', $main_sub_menu);

    }
    /**
    * About Menu.
    *
    * @author brice
    * @since June 13, 2016
    * @return void
    */
    protected static function addAboutMenu()
    {
        $about_menu = false;
        $route = Route::currentRouteName();
        $conf = Config::get('menu');

        $about_menu = $conf['about_menu'];
        if ($about_menu) {
            $start = true;
            foreach ( $about_menu as $key => &$root) {
                $root['pos'] = false;
                $root['active'] = false;

                if ($start) {
                    $root['pos'] = 'start';
                    $start = false;
                }

                if (isset($root['route'])) {
                    if ($root['route'] == $route) {
                        $root['active'] = true;
                    }
                } else {
                    $root['route'] = false;
                }

                if (!isset($root['icon'])) {
                    $root['icon'] = false;
                }
            }
            $root['pos'] = 'last';
        }
        view()->share('about_menu', $about_menu);
    }
}