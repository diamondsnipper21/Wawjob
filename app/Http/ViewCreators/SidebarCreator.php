<?php namespace iJobDesk\Http\ViewCreators;

use Illuminate\Contracts\View\View;
use Illuminate\Users\Repository as UserRepository;

use Config;
use Route;

class SidebarCreator
{

    /**
    * Bind data to the view.
    *
    * @param  View  $view
    * @return void
    */
    public static function create()
    {
        self::addSidebar();
    }

    /**
    * Add the sidebar.
    *
    * @author Sunlight
    * @since Jan 13, 2015
    * @return void
    */
    public static function addSidebar()
    {
        $conf = Config::get('menu');
        $route = Route::currentRouteName();
        $start = true;

        foreach ($conf['sidebar'] as $key => &$root) {
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
                } else if (isset($root['alternates'])) {
                    foreach ($root['alternates'] as $alt_root) {
                        if ($alt_root == $route) {
                            $root['active'] = true;
                            break;
                        }
                    }
                }
            } else {
                $root['route'] = false;
            }

            if (!isset($root['icon'])) {
                $root['icon'] = false;
            }

            if ($has_children) {
                foreach ($root['children'] as $child_key => &$child) {
                    $child['active'] = false;
                    if (isset($child['route'])) {
                        if ($child['route'] == $route) {
                            $child['active'] = true;
                            $root['active'] = true;
                        } else if (isset($child['alternates'])) {
                            foreach ($child['alternates'] as $alt_root) {
                                if ($alt_root == $route) {
                                    $child['active'] = true;
                                    $root['active'] = true;
                                    break;
                                }
                            }
                        }
                    } else {
                        $child['route'] = false;
                    }
                    if (!isset($child['icon'])) {
                        $child['icon'] = false;
                    }
                }
            } else {
                $root['children'] = false;
            }
        }
        $root['pos'] = 'last';

        view()->share('sidebar', $conf['sidebar']);
    }
}