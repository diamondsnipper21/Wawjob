<?php namespace iJobDesk\Http\Controllers;

use iJobDesk\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Log;

use iJobDesk\Models\User;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\Unsubscribe;

class EmailMarketingController extends Controller {
	/**
	* Unsubscribe
	*
	* @return Response
	*/
	public function unsubscribe(Request $request, $token) {
		$token = decrypt_string($token);

		list($slug, $email) = explode(',', $token);

		if (!$slug || !$email)
			abort(404);

		$page_key = 'ignore';
		if (EmailTemplate::isForGuest($slug)) {
			if (!self::isUnsubscribe($email)) {
				$model = new Unsubscribe();
				$model->email = $email;
				$model->save();
			}

			$page_key = 'guest';
		} elseif (EmailTemplate::isForUser($slug)) {
			$user = User::where('email', $email)->first();

			if (!$user) {
				Log::error("EmailMarketingController@unsubscribe: No User, slug=$slug, email=$email");
				abort(404);
			}

			if ($user->isSuspended())
				abort(404);

			if ($user->isBuyer())
				$setting_key = EmailTemplate::setting_key('Buyer', $slug);
			elseif ($user->isFreelancer())
				$setting_key = EmailTemplate::setting_key('Freelancer', $slug);

			if (!$setting_key)
				$setting_key = EmailTemplate::setting_key('General', $slug);

			if (!$setting_key) {
				Log::error("EmailMarketingController@unsubscribe: No Notification Setting Key, slug=$slug, email=$email");
				abort(404);
			}

			$user->userNotificationSetting->$setting_key = 0;
			$user->userNotificationSetting->save();

			$page_key = 'user';
		}

		return view('pages.frontend.unsubscribe', [
			'page'     => 'frontend.unsubscribe',
			'page_key' => $page_key,
		]);
	}

	/**
	 * Check this email is unsubscribed or not
	 */
	public static function isUnsubscribe($email) {
		return Unsubscribe::where('email', $email)->exists();
	}
}
