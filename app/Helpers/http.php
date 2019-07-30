<?php
/**
* HTTP services
*
* Mar 11, 2016 - sunlignt
*/

use iJobDesk\Models\User;
use iJobDesk\Models\Contract;
use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectMessageThread;
use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\Ticket;

if ( !function_exists('send_jwt') ) {
	function send_jwt($url, $jwt, $method = 'GET', $opts = [])
	{
		$defaults = array(
			CURLOPT_HEADER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 4,
			CURLOPT_URL => $url,
			CURLOPT_CUSTOMREQUEST => $method, // GET POST PUT PATCH DELETE HEAD OPTIONS
			CURLOPT_HTTPHEADER => [
				"X-CSRF-TOKEN:" . csrf_token(),
				"JWT:" . $jwt
			],
      	);

		$ch = curl_init();
		curl_setopt_array($ch, ($opts + $defaults));
		if (!$result = curl_exec($ch)) {
			return [
				'error' => 'Failed to get data, please try again.',
			];
		}
		curl_close($ch);

		return json_decode($result, true);
	}
}

if ( !function_exists('makeUrlParams') ) {
	function makeUrlParams($params) {
		if ( !$params ) {
			return '';
		}

		$array = [];
		foreach ( $params as $k => $v ) {
			if ( $v != '' ) {
				$array[] = $k . '=' . $v;
			}
		}

		if ( !$array ) {
			return '';
		}

		return implode('&', $array);
	}
}

if ( !function_exists('_route') ) {
	function _route($name, $parameters = array(), $absolute = true, $route = null, $current_user = null, $use_frontend_url = false) {
		$blank_url = 'javascript:void(0)';

		if (!$current_user)
			$current_user = Auth::user();

		if ($current_user && $current_user->isAdmin()) {

			if ($name == 'user.profile') {
				$user_id = array_key_exists('uid', $parameters)?$parameters['uid']:$parameters[0];

				$user = User::find($user_id);

				if ($current_user->isTicket() || $user->isAdmin())
					return $blank_url;

				$name = 'admin.super.user.overview';
				$parameters = ['user_id' => $user_id];
			}

			if ($name == 'contract.contract_view') {
				if (array_key_exists('user_id', $parameters))
					$name = 'admin.super.user.contract';
				else {
					$name = 'admin.super.contract';
					unset($parameters['user_id']);
				}
			}

			if ($name == 'job.review_proposals') {
				if (array_key_exists('user_id', $parameters))
					$name = 'admin.super.user.buyer.job.proposal';
				else {
					$name = 'admin.super.job.proposal';
					unset($parameters['user_id']);
				}
			}

			if ($name == 'job.review_proposals_page') {
				if (array_key_exists('user_id', $parameters))
					$name = 'admin.super.user.buyer.job.proposal_page';
				else {
					$name = 'admin.super.job.proposal_page';
					unset($parameters['user_id']);
				}
			}

			if ($name == 'job.interviews') {
				if (array_key_exists('user_id', $parameters))
					$name = 'admin.super.user.buyer.job.interview';
				else {
					$name = 'admin.super.job.interview';
					unset($parameters['user_id']);
				}
			}

			if ($name == 'job.interviews_page') {
				if (array_key_exists('user_id', $parameters))
					$name = 'admin.super.user.buyer.job.interview_page';
				else {
					$name = 'admin.super.job.interview_page';
					unset($parameters['user_id']);
				}
			}

			if ($name == 'job.hire_offers') {
				if (array_key_exists('user_id', $parameters))
					$name = 'admin.super.user.buyer.job.hire_offers';
				else {
					$name = 'admin.super.job.hire_offers';
					unset($parameters['user_id']);
				}
			}

			if ( $use_frontend_url ) {
				if ($name == 'job.view') {
					$id = array_key_exists('id', $parameters)?$parameters['id']:$parameters[0];
					$job = Project::find($id);
					if ( $job ) {
						$parameters['id'] = $job->unique_id;
					}
				}
			}
		} else {
			if ($name == 'user.profile' || $name == 'job.hire_user') {
				$user_id = array_key_exists('uid', $parameters)?$parameters['uid']:$parameters[0];

				$user = User::find($user_id);
				if ( !$user || !$user->isFreelancer() )
					return $blank_url;

				if (!$user->unique_id) {
					$user->unique_id = str_replace('_', '-', $user->username);
					$user->save();
				}

				$parameters = [];
				$parameters['uid'] = $user->unique_id;				
			} elseif ($name == 'contract.contract_view') {
				$id = array_key_exists('id', $parameters)?$parameters['id']:$parameters[0];

				$contract = Contract::find($id);

				if ( !$contract ) {
					abort(404);
				}

				if (!$contract->unique_id) {
					$contract->unique_id = generate_unique_id($contract->id);
					$contract->save();
				}

				$parameters = [];
				$parameters['id'] = $contract->unique_id;
			} elseif (in_array($name, ['job.edit', 'job.apply', 'job.edit.repost', 'job.hire', 'job.change_status.ajax', 'job.change_public.ajax', 'job.view', 'job.overview', 'job.invite_freelancers', 'job.invite_freelancers_page', 'job.interviews', 'job.interviews_page', 'job.hire_offers'])) {
				$id = array_key_exists('id', $parameters)?$parameters['id']:$parameters[0];

				$job = Project::find($id);

				if (!$job->unique_id) {
					$job->unique_id = generate_unique_id($job->id);
					$job->save();
				}

				if (!in_array($name, ['job.edit.repost', 'job.hire', 'job.change_status.ajax', 'job.change_public.ajax', 'job.invite_freelancers_page', 'job.interviews_page']))
					$parameters = [];

				if ($name == 'job.hire') {
					$user = User::find($parameters['uid']);

					if (!$user->unique_id) {
						$user->unique_id = generate_unique_id($user->id);
						$user->save();
					}

					$parameters['uid'] = $user->unique_id;

					if (array_key_exists('pid', $parameters)) {
						$application = ProjectApplication::find($parameters['pid']);

						if (!$application->unique_id) {
							$application->unique_id = generate_unique_id($application->id);
							$application->save();
						}

						$parameters['pid'] = $application->unique_id;
					}
				}

				$parameters['id'] = $job->unique_id;
			} elseif ($name == 'job.application_detail') {
				$id = array_key_exists('id', $parameters)?$parameters['id']:$parameters[0];

				$application = ProjectApplication::find($id);

				if (!$application->unique_id) {
					$application->unique_id = generate_unique_id($application->id);
					$application->save();
				}

				$parameters = [];
				$parameters['id'] = $application->unique_id;
			} elseif ($name == 'message.list' && !empty($parameters)) {
				$id = array_key_exists('id', $parameters)?$parameters['id']:$parameters[0];

				$thread = ProjectMessageThread::find($id);

				if (!$thread->unique_id) {
					$thread->unique_id = generate_unique_id($thread->id);
					$thread->save();
				}

				$parameters = [];
				$parameters['id'] = $thread->unique_id;
			} elseif ($name == 'workdiary.view') {
				$id = array_key_exists('cid', $parameters)?$parameters['cid']:$parameters[0];

				$contract = Contract::find($id);

				if (!$contract->unique_id) {
					$contract->unique_id = generate_unique_id($contract->id);
					$contract->save();
				}

				$parameters = [];
				$parameters['cid'] = $contract->unique_id;
			} elseif ($name == 'ticket.detail') {
				$id = array_key_exists('id', $parameters)?$parameters['id']:$parameters[0];

				$ticket = Ticket::find($id);

				if (!$ticket->unique_id) {
					$ticket->unique_id = generate_unique_id($ticket->id);
					$ticket->save();
				}

				$parameters = [];
				$parameters['id'] = $ticket->unique_id;
			}
		}
		return route($name, $parameters, $absolute, $route);
	}
}