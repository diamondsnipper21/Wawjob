<?php namespace iJobDesk\Http\Controllers;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Auth;
use Config;
use DB;
use Validator;

use iJobDesk\Models\User;
use iJobDesk\Models\EmailTemplate;
use iJobDesk\Models\ContactUs;
use iJobDesk\Models\AdminMessage;
use iJobDesk\Models\StaticPage;
use iJobDesk\Models\HelpPage;
use Mews\Captcha\Captcha;

use iJobDesk\Models\Project;
use iJobDesk\Models\Contract;
use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\Settings;

class HomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's front page for users.
	| It is just here to get your app started!
	|
	*/

	/**
	 * Show the application front page to the user.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		// dd(generate_unique_id(1));
    	$user = Auth::user();
    	if ($user && $user->isAdmin()) {
    		$redirect = getRedirectByRole($user);

    		return redirect()->route($redirect);
        }

        // Just change the home url to dashboard
        // if ($user)
        // 	return $this->dashboard($request);

		return view('pages.home', [
			'page' => 'home',
			'j_trans' => [
                'find_freelancers' => trans('search.find_freelancers'),
                'find_jobs' => trans('search.find_jobs'),
            ],
		]);
	}

	/**
	 * Show the contact us page.
	 *
	 * @return Response
	 */
	public function contact_us(Request $request) {
		if ( Auth::user() )
			return redirect()->to(route('ticket.list') . '?_action=new');

    	$subject 	= null;
		$email 	 	= null;
		$fullname 	= null;
		$content 	= null;

		$sent = false;
		$captcha_result = true;

		if ( $request->isMethod('post') ) {

			$validator = Validator::make($request->all(), [
				'subject' 	=> 'required|max:200',
				'fullname' 	=> 'required|max:50',
				'email' 		=> 'required|email',
				'content' 		=> 'required|max:5000'
			]);

			if ( $validator->fails() ) {
				$errors = $validator->messages();
				if ( $errors->all() ) {
					foreach ( $errors->all() as $error ) {
						add_message($error, 'danger');
					}
				}
			} else {
				$subject 	= $request->input('subject', null);
				$fullname	= $request->input('fullname', null);
				$email 	 	= $request->input('email', null);
				$content 	= $request->input('content', null);

				$captcha = $request->input('captcha');
				if ( !app('captcha')->check($captcha) ) {
					$captcha_result = false;
				}

				if ( $captcha_result ) {
					// Check if user is existed in our site
					$user_existed = User::where('email', $email)->first();

		            if ( $user_existed ) {
		            	$contact_us = new ContactUs();
		            	$contact_us->subject 	= $subject;
		            	$contact_us->fullname 	= $fullname;
		            	$contact_us->email 		= $email;
		            	$contact_us->created_at = date('Y-m-d H:i:s');

		            	$contact_us->save();

		            	$message = new AdminMessage();
		            	$message->message_type 	= AdminMessage::MESSAGE_TYPE_CONTACT;
		            	$message->target_id 	= $contact_us->id;
		            	$message->message 		= $content;

		            	$message->save();

						$sent = EmailTemplate::contactUs($contact_us, $content);

		            	$subject = null;
						$fullname= null;
						$email 	 = null;
						$content = null;
		            } else {
		            	add_message(trans('page.frontend.contact_us.error_user'), 'danger');
		            }
		        } else {
		        	add_message(trans('page.frontend.contact_us.error_captcha'), 'danger');
		        }
			}

	        $request->flash();
		}

    	return view('pages.frontend.contact_us', [
			'page' => 'frontend.contact_us',

			'subject' 	=> $subject,
			'fullname' 	=> $fullname,
			'email'   	=> $email,
			'content' 	=> $content,
			'captcha_result' 	=> $captcha_result,

			'sent'		=> $sent,

			'contact_email' => Settings::get('CONTACT_EMAIL_ADDRESS'),
			'company_address' => Settings::get('COMPANY_ADDRESS'),
		]);
	}

	/**
	 * Display static page.
	 */
	public function static_page(Request $request, $slug) {
		$static_page = StaticPage::where('slug', $slug)->first();

		if (!$static_page)
			abort(404);

		return view('pages.frontend.static_page', [
			'page' 			=> 'frontend.static_page',
			'static_page' 	=> $static_page
		]);
	}

	/**
	 * Show how-it-works page.
	 *
	 * @return Response
	 */
	public function how_it_works() {
    	return view('pages.frontend.how_it_works', [
			'page' => 'frontend.how_it_works',
		]);
	}

	public function download_tools(Request $request) {
		if (!Auth::check()) {
			return redirect()->guest('login');
		}

		if ( $request->isMethod('post') ) {
			$root = getRoot();

			$path = null;
			$name = null;
			if ( $request->input('windows') ) {
				$name = 'iJobDeskSetup.exe';
				$path = $root . '/TrackerApp/Windows/' . $name;
			} else if ( $request->input('mac') ) {
				$name = 'iJobDesk.dmg';
				$path = $root . '/TrackerApp/MacOS/' . $name;
			} else if ( $request->input('linux_version') ) {

				$availVersions = ['debian_64', 'rpm_64', 'debian_32', 'rpm_32'];
				$version = $request->input('linux_version');

				if (in_array($version, $availVersions)) {

					$targetDirPath = $root . '/TrackerApp/Linux/';
					$fileNameList = glob($targetDirPath . '*.*');
					$nameList = [];
					if (count($fileNameList) > 0) {
						foreach ($fileNameList as $fileName) {
							$fileName = str_replace($targetDirPath, '', $fileName);

							if (strpos($fileName, 'amd64.deb') !== FALSE)
								$nameList['debian_64'] = $fileName;
							else if (strpos($fileName, 'i386.deb') !== FALSE)
								$nameList['debian_32'] = $fileName;
							else if (strpos($fileName, 'x86_64.rpm') !== FALSE)
								$nameList['rpm_64'] = $fileName;
							else if (strpos($fileName, 'i386.rpm') !== FALSE)
								$nameList['rpm_32'] = $fileName;
								
						}

						if (isset($nameList[$version])) {
							$name = $nameList[$version];
							$path = $root . '/TrackerApp/Linux/' . $name;
						}						
					}						
				}

				
			}

			if ( $path && file_exists($path) ) {
				$mime_type = getMimeType($path);

				header('Cache-Control: max-age=86400');
				header('Content-type: '. $mime_type);
				header('Content-Length: ' . filesize($path));
				header('Content-Disposition: attachment; filename=' . $name);
				readfile($path);
				exit;
			}
		}

    	return view('pages.frontend.download_tools', [
			'page' => 'frontend.download_tools',
		]);
	}

	public function dashboard(Request $request) {
		$user = Auth::user();

		if ($user->isBuyer()) {
			$job_postings = Project::where('client_id', $user->id)
								   ->whereIn('status', [Project::STATUS_OPEN, Project::STATUS_SUSPENDED])
								   ->where('accept_term', Project::ACCEPT_TERM_YES)
								   ->count();

			$contracts = Contract::where('buyer_id', $user->id)
						          ->whereIn('status', [
							          	Contract::STATUS_OPEN, 
							          	Contract::STATUS_PAUSED, 
							          	Contract::STATUS_SUSPENDED
							      ])
							      ->count();


			$offer_sents = Contract::where('buyer_id', $user->id)
				          		   ->where('status', Contract::STATUS_OFFER)
				          		   ->count();

			view()->share([
				'job_postings' 	=> $job_postings,
				'contracts' 	=> $contracts,
				'offer_sents' 	=> $offer_sents,				
			]);
		} else {
			$job_offers = Contract::where('contractor_id', $user->id)
								  ->where('status', Contract::STATUS_OFFER)
								  ->count();

			$proposals = ProjectApplication::where('user_id', $user->id)
											  ->where('status', ProjectApplication::STATUS_NORMAL)
											  ->where('is_declined', ProjectApplication::IS_DECLINED_NO)
											  ->count();

			$contracts = Contract::where('contractor_id', $user->id)
						         ->whereIn('status', [
							          	Contract::STATUS_OPEN, 
							          	Contract::STATUS_PAUSED, 
							          	Contract::STATUS_SUSPENDED
							     ])
							     ->count();

			view()->share([
				'job_offers' 	=> $job_offers,
				'proposals' 	=> $proposals,
				'contracts' 	=> $contracts
			]);
		}

		$wallet = $user->myBalance(false);
        $amountUnderHolding = $user->isBuyer() ? $user->getTotalAmountUnderWorkAndReview() : 0;
        $balance = $wallet - $amountUnderHolding;

        // Show congratulation modal after finishing to setup profile
        $show_congratulation = session('show_congratulation', false);
        session()->forget('show_congratulation');

    	return view('pages.frontend.dashboard', [
			'page' => 'frontend.dashboard',
			'balance' => $wallet,
			'holding_amount' => $amountUnderHolding,
			'show_congratulation' => $show_congratulation
		]);
	}

	/**
	 * Help Page
	 */
	public function help(Request $request, $slug = null) {
		if (!empty($slug)) {
			$help_page = HelpPage::where('slug', $slug)->first();

			if (!$help_page)
				abort(404);

			if ($help_page->content == '{"en":null,"ch":null}') // parent category without content
				return redirect()->route('frontend.help');

			view()->share([
				'help_page' 	=> $help_page
			]);
		}

		return view('pages.frontend.help', [
			'page' => 'frontend.help'
		]);
	}

	public function help_search(Request $request) {
		$q = $request->input('q');
		$page = $request->input("page", 1);

		if (empty(trim($q)))
			return redirect()->route('frontend.help');

		$per_page = Config::get('settings.freelancer.per_page');
		$start = ($page - 1) * $per_page;

		$raw_query = "SELECT [param] FROM help_pages WHERE (id NOT IN (SELECT parent_id FROM help_pages) AND id NOT IN (SELECT second_parent_id FROM help_pages)) AND (title LIKE '%$q%' OR content LIKE '%$q%')";

		$columns = "
			*,
			IF(title LIKE '%\":\"$q %' OR title LIKE '% $q %' OR title LIKE '% $q\"}%' OR title LIKE '$q', 1, 0) AS order1,
			IF(content LIKE '%\":\"$q %' OR content LIKE '% $q %' OR content LIKE '% $q\"}%' OR content LIKE '$q', 1, 0) AS order2,
			IF(title LIKE '%$q%', 1, 0) AS order3 ,
			IF(content LIKE '%$q%', 1, 0) AS order4 
		";

		$results = DB::select(DB::raw(str_replace('[param]', $columns, $raw_query) . " ORDER BY order1 DESC, order2 DESC, order3 DESC, order4 DESC, `order`, `second_order` LIMIT $start, $per_page"));

		// dd(str_replace('[param]', '*', $raw_query) . " ORDER BY (order1) + (order2) + (order3) LIMIT $start, $per_page"));
		$totals = DB::select(DB::raw(str_replace('[param]', 'COUNT(*) AS count', $raw_query)));
		$totals = $totals[0]->count;

		$paginator = new \Illuminate\Pagination\LengthAwarePaginator($results, $totals, $per_page, $page, [
		    'path'  => $request->url(),
		    'query' => $request->query(),
		]);

		return view('pages.frontend.help', [
			'page' => 'frontend.help',
			'q' => $q,
			'pages' => $paginator
		]);
	}

	public function coming_soon(Request $request) {
		return view('pages.frontend.coming_soon', [
			'page' => 'frontend.coming_soon',
		]);
	}

	public function paypal(Request $request) {
		return view('pages.frontend.paypal', [
			'page' => 'frontend.paypal',
		]);
	}
}