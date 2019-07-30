<?php namespace iJobDesk\Http\ViewCreators;

use Illuminate\Contracts\View\View;
use Illuminate\Users\Repository as UserRepository;

use Config;
use Route;

use Auth;

use iJobDesk\Models\User;

class UserViewCreator
{

    /**
    * Bind data to the view.
    *
    * @param  View  $view
    * @return void
    */
    public static function create()
    {
        self::addUserAvatarUrl();
    }

    /**
    * User Avatar.
    *
    * @author nada
    * @since Jan 22, 2015
    * @return void
    */
    protected static function addUserAvatarUrl()
    {
        $user = Auth::user();

        $url_user_avatar = '';
        if ($user && $user->contact && $user->contact->avatar) {
            $url_user_avatar = $user->contact->avatar."?t=".time();
        }

        $_user = false;
        if ($user && $user->id) {
            $_user = $user;
        }

        view()->share('url_user_avatar', $url_user_avatar);
    }
}