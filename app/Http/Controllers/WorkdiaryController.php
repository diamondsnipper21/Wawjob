<?php namespace iJobDesk\Http\Controllers;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Auth;
use Storage;
use Config;
use Session;

// Models
use iJobDesk\Models\Contract;
use iJobDesk\Models\HourlyLog;
use iJobDesk\Models\Project;
use iJobDesk\Models\User;

class WorkdiaryController extends Controller {

    public function view_first(Request $request, $user = null) {
        if (empty($user))
            $user = Auth::user();

        view()->share([
            'user' => $user
        ]);

        if ( $user->isBuyer() ) {
            return $this->view_first_buyer($request, $user);
        } elseif ( $user->isFreelancer() ) {
            return $this->view_first_freelancer($request, $user);
        }
    }

    public function view(Request $request, $cid, $user = null) {

        $contract = Contract::find($cid);
        if (!Auth::user()->isAdmin()) {
            $contract = Contract::findByUnique($cid);
            $cid = $contract->id;
        }

        if ( empty($contract) )
            abort(404);

        if (empty($user))
            $user = Auth::user();

        view()->share([
            'user' => $user
        ]);

        if ( !$contract->isHourly() || $contract->isClosed() ) {
        	return redirect()->route('workdiary.view_first');
        }

        if ( $user->isFreelancer() && !$contract->checkCurrentFreelancer($user->id) ) {
            abort(404);
        }

        if ( $user->isBuyer() && !$contract->checkIsAuthor($user->id) ) {
            abort(404);
        }

        if ( $user->isBuyer() ) {
            return $this->view_buyer($request, $cid, $user);
        } elseif ( $user->isFreelancer() ) {
            return $this->view_freelancer($request, $cid, $user);
        }
    }

    protected function view_first_buyer(Request $request, $user = null) {
        if (empty($user))
            $user = Auth::user();

        $contract = Contract::where('buyer_id', $user->id)
                            ->whereIn('status', [
                                Contract::STATUS_OPEN, 
                                Contract::STATUS_PAUSED,
                                Contract::STATUS_SUSPENDED,
                            ])
                            ->where('type', Project::TYPE_HOURLY)
                            ->orderBy('started_at', 'asc')
                            ->first();
        if ( $contract ) {
            return $this->view_buyer($request, $contract->id, $user);
        }

        return $this->view_buyer($request, 0, $user);
    }

    protected function view_buyer(Request $request, $cid, $user = null) {

        $server_timezone = date_default_timezone_get();
		$user_timezone_name = $server_timezone;

        // Get user timezone
        $timezones = [];

        $t = $user->contact->timezone;
        if ($t && intval($t->gmt_offset) != 0) {
            $label = timezoneToString($t->gmt_offset);
            $timezones[$label] = $t->name;
            $user_timezone_name = $t->name;
        }

        list($from, $to) = weekRange('now', 'Y-m-d', $user_timezone_name);

        // Timezone
        $tz = $request->input('tz', $user_timezone_name);

        $today = convertTz(date('Y-m-d H:i:s'), $tz, $server_timezone, 'Y-m-d');

        // Work diary Date
        $wdate = $request->input('wdate');
        if ( !$wdate || !strtotime($wdate) ) {
            $wdate = $today;
        }

        // Show mode (grid | list)
        $mode = $request->input("mode");
        if ( !$mode ) {
            $mode = isset($_COOKIE['workdiary_mode']) ? $_COOKIE['workdiary_mode'] : 'grid';
        }

        $diary = null;
        $meta = [];

        // Calculate prev, next URL
        $dates = [
            'prev' => date('Y-m-d', strtotime($wdate) - 86400),
            'next' => date('Y-m-d', strtotime($wdate) + 86400),
            'today' => $today
        ];

        foreach ($dates as $k => $date ) {
            $dates[$k] = $request->url() . "?wdate=$date&tz=$tz";
        }

        $next_disabled = false;
        $prev_disabled = false;
        $today_disabled = false;

        if ( $wdate == $today ) {
        	$today_disabled = $next_disabled = true;
        }

        if ( $wdate > $today ) {
        	return redirect()->route('workdiary.view_first');
        }

        $contract = false;

        if ( $cid ) {
        	$contract = Contract::findOrFail($cid);
        	if ( !$contract ) {
        		return redirect()->route('workdiary.view_first');
        	}

            $request->flash();

            // Get screenshots and meta
            $data = HourlyLog::getDiary($contract->id, $wdate, $tz);
            $info = $data[0];
            $diary = $data[1];

            // Data to pass to view
            $meta = [
                'mode' => $mode,
                'wdate' => $wdate,
                'maxSlot' => HourlyLog::getMaxSlot(),
                'tz' => $tz,

                'time' => [
                    'total' => formatMinuteInterval($info['total']),
                    'auto' => formatMinuteInterval($info['auto']),
                    'manual' => formatMinuteInterval($info['manual']),
                    'overlimit' => formatMinuteInterval($info['overlimit']),
                ],

                'dateUrls' => $dates,
            ];

        } else {
            $meta = [
                'mode' => $mode,
                'wdate' => $wdate,
                'maxSlot' => HourlyLog::getMaxSlot(),
                'tz' => $tz,

                'time' => [
                    'total' => 0,
                    'auto' => 0,
                    'manual' => 0,
                    'overlimit' => 0,
                ],

                'dateUrls' => $dates,
            ];
        }

        // Contract Selector
        $_contracts = Contract::where('buyer_id', $user->id)
                                ->whereIn('status', [
                                    Contract::STATUS_OPEN, 
                                    Contract::STATUS_PAUSED,
                                    Contract::STATUS_SUSPENDED,
                                ])
                                ->where('type', Project::TYPE_HOURLY)
                                ->orderBy('started_at', 'asc')
                                ->get();
        
        $contracts = [];
        if ( count($_contracts) ) {
        	foreach ( $_contracts as $c ) {
    			$contracts[$c->project->subject][] = $c;
        	}
        }

        $log_dates = [];
        
        if ( $contract ) {
            // worked history
            $log_dates = HourlyLog::dateHistory($contract->id, $wdate, $tz);
        }

        return view('pages.buyer.workdiary.view', [
            'page' => (Auth::user()->isAdmin() ? 'super.user.buyer.workdiary' : 'buyer.workdiary.view'),
            'meta' => $meta,
            'diary' => $diary,
            'options' => [
                'tz' => $timezones
            ], 
            
            'contracts' => $contracts,
            'contract' => $contract,
            'next_disabled' => $next_disabled,
            'prev_disabled' => $prev_disabled,
            'today_disabled' => $today_disabled,
            
            'log_dates' => $log_dates,
            'started_at' => $contract ? convertTz($contract->started_at, $tz, $server_timezone, 'm/d/Y') : convertTz(date('Y-m-d H:i:s'), $tz, $server_timezone, 'm/d/Y'),
        ]);
    }

    public function ajaxAction(Request $request)
    {
        if ( !$request->ajax() ) {
            return false;
        }

        $cmd = $request->input("cmd");
        if ($cmd == "loadSlot") {
            $sid = $request->input("sid");
            $tz = $request->input('tz');
            $act = HourlyLog::getSlotInfo($sid, $tz);

            if ( !$act ) {
                return response()->json([
                    'success' => false
                ]);
            }

            if (count($act) >= 10) {
                $col = ceil(count($act) / 2);

                $act1 = array_splice($act, 0, $col);
                $act2 = $act;

                $html = view('pages.buyer.workdiary.modal.act_table', [
                    'act' => $act1,
                    'class' => 'two-col'
                ])->render();

                $html .= view('pages.buyer.workdiary.modal.act_table', [
                    'act' => $act2,
                    'class' => 'two-col'
                ])->render();
            } else {
                $html = view('pages.buyer.workdiary.modal.act_table', [
                    'act' => $act,
                    'class' => 'one-col'
                ])->render();
            }

            return response()->json([
                'success' => true,
                'sid' => $sid,
                'html' => $html
            ]);
        }

        return false; 
    }

    /*
    *   @Auth Ri Chol Min
    *   @Date 03/21/2016
    */
    protected function view_first_freelancer(Request $request, $user = null) {
        if (empty($user))
            $user = Auth::user();

        $contract = Contract::where('contractor_id', $user->id)
		            ->whereIn('status', [
                        Contract::STATUS_OPEN, 
                        Contract::STATUS_PAUSED,
                        Contract::STATUS_SUSPENDED,
                    ])
		            ->where('type', Project::TYPE_HOURLY)
		            ->orderBy('started_at', 'asc')
		            ->first();

        if ( $contract ) {
            return $this->view_freelancer($request, $contract->id, $user);
        }

        return $this->view_freelancer($request, 0, $user);
    }

    protected function view_freelancer(Request $request, $cid, $user = null) {

        $server_timezone = date_default_timezone_get();
		$user_timezone_name = $server_timezone;

        // Get user timezone
        $timezones = [];

        $t = $user->contact->timezone;
        if ($t && intval($t->gmt_offset) != 0) {
            $label = timezoneToString($t->gmt_offset);
            $timezones[$label] = $t->name;
            $user_timezone_name = $t->name;
        }

        list($from, $to) = weekRange('now', 'Y-m-d', $user_timezone_name);

        // Timezone
        $tz = $request->input('tz', $user_timezone_name);

        $today = convertTz(date('Y-m-d H:i:s'), $tz, $server_timezone, 'Y-m-d');

        // Work diary Date
        $wdate = $request->input('wdate');
        if ( !$wdate || !strtotime($wdate) ) {
            $wdate = $today;
        }
        
        list($server_week_from, $server_week_to) = weekRange('now', 'Y-m-d');
        $is_this_week = ( $wdate >= $server_week_from && $wdate <= $server_week_to );

        // Show mode (grid | list)
        $mode = $request->input('mode');
        if ( !$mode ) {
            $mode = isset($_COOKIE['workdiary_mode']) ? $_COOKIE['workdiary_mode'] : 'grid';
        }  

        $diary = null;
        $meta = [];

        // Calculate prev, next URL
        $dates = [
            'prev' => date('Y-m-d', strtotime($wdate) - 86400),
            'next' => date('Y-m-d', strtotime($wdate) + 86400),
            'today' => $today
        ];

        foreach($dates as $k => $date) {
            $dates[$k] = $request->url() . "?wdate=$date&tz=$tz";
        }

        $next_disabled = false;
        $prev_disabled = false;
        $today_disabled = false;

        if ( $wdate == $today ) {
        	$today_disabled = $next_disabled = true;
        }

        if ( $wdate > $today ) {
        	return redirect()->route('workdiary.view_first');
        }

        $contract = false;

        if ( $cid ) {
        	$contract = Contract::findOrFail($cid);
        	if ( !$contract ) {
        		return redirect()->route('workdiary.view_first');
        	}

            $started_at = convertTz($contract->started_at, $tz, $server_timezone, 'Y-m-d');
            
            if ( $wdate < $started_at ) {
                $prev_disabled = true;
            }
        }

        if ( $contract ) {
            $request->flash();

            // Get screenshots and meta
            $data = HourlyLog::getDiary($contract->id, $wdate, $tz);
            $info = $data[0];
            $diary = $data[1];

            // Data to pass to view
            $meta = [
                'mode' => $mode,
                'wdate' => $wdate,
                'tz' => $tz,
                'maxSlot' => HourlyLog::getMaxSlot(),

                'time' => [
                    'total' => formatMinuteInterval($info['total']),
                    'auto' => formatMinuteInterval($info['auto']),
                    'manual' => formatMinuteInterval($info['manual']),
                    'overlimit' => formatMinuteInterval($info['overlimit']),
                ],

                'dateUrls' => $dates,
            ];
        } else {
            $meta = [
                'mode' => $mode,
                'wdate' => $wdate,
                'tz' => $tz,
                'maxSlot' => HourlyLog::getMaxSlot(),

                'time' => [
                    'total' => 0,
                    'auto' => 0,
                    'manual' => 0,
                    'overlimit' => 0,
                ],

                'dateUrls' => $dates,
            ];
        }

        // Contract Selector
        $contracts = Contract::where('contractor_id', $user->id)
                            ->whereIn('status', [
                                Contract::STATUS_OPEN, 
                                Contract::STATUS_PAUSED,
                                Contract::STATUS_SUSPENDED,
                            ])
                            ->where('type', Project::TYPE_HOURLY)
                            ->orderBy('started_at', 'asc')
                            ->get();

        $log_dates = [];
        
        if ( $contract ) {
            $log_dates = HourlyLog::dateHistory($contract->id, $wdate);
        }

        return view('pages.freelancer.workdiary.viewjob', [
            'page' => (Auth::user()->isAdmin() ? 'super.user.freelancer.workdiary' : 'freelancer.workdiary.viewjob'),
            'config' => Config::get('settings'),
            'meta' => $meta,
            'diary' => $diary,
            'options' => [
                'tz' => $timezones
            ], 
            'contracts' => $contracts,
            'contract' => $contract,      
            'is_this_week' => $is_this_week,

            'next_disabled' => $next_disabled,
            'prev_disabled' => $prev_disabled,
            'today_disabled' => $today_disabled,

            'log_dates' => $log_dates,
            'started_at' => $contract ? convertTz($contract->started_at, $tz, $server_timezone, 'm/d/Y') : convertTz(date('Y-m-d H:i:s'), $tz, $server_timezone, 'm/d/Y'),

            'j_trans'=> [
                'delete_screenshot' => trans('j_message.freelancer.workdiary.delete_screenshot'), 
                'select_screenshot' => trans('j_message.freelancer.workdiary.select_screenshot')
            ]
        ]);
    }

    public function ajaxjobAction(Request $request) {
        $user = Auth::user();

        if ( !$request->ajax() ) {
            return false;
        }

        $cmd = $request->input('cmd');
        switch ($cmd) {
            case 'loadSlot':
                $sid = $request->input('sid');
                $tz = $request->input('tz');
                $act = HourlyLog::getSlotInfo($sid, $tz);

                if ( !$act ) {
                    return response()->json([
                        'success' => false
                    ]);
                }

                if (count($act) >= 10) {
                    $col = ceil(count($act) / 2);

                    $act1 = array_splice($act, 0, $col);
                    $act2 = $act;

                    $html = view('pages.freelancer.workdiary.modal.act_table', [
                        'act' => $act1,
                        'class' => 'two-col'
                    ])->render();

                    $html .= view('pages.freelancer.workdiary.modal.act_table', [
                        'act' => $act2,
                        'class' => 'two-col'
                    ])->render();
                } else {
                    $html = view('pages.freelancer.workdiary.modal.act_table', [
                        'act' => $act,
                        'class' => 'one-col'
                    ])->render();
                }

                return response()->json([
                    'success' => true,
                    'sid' => $sid,
                    'html' => $html
                ]);

            case 'deleteSlot':

                if ( $user->isAdmin() ) {
                    abort(404);
                }

                $sid = $request->input('sid');
                if ( empty($sid) ) {
                    return $this->failed('No screenshot IDs given.');
                }

                $cid = $request->input('cid');
                $date = $request->input('date');

                if ( !HourlyLog::deleteSlot($user->id, $cid, $sid) ) {
                    return $this->failed('Failed to delete screenshots.');
                }

                $n = count($sid);

                return response()->json([
                    'success' => true,
                    'msg' => 'Successfully deleted ' . $n . ' ' . str_plural('screenshot', $n)
                ]);

            case 'editMemo':
                $cid = $request->input('cid');
                $date = $request->input('date');
                $sid = $request->input('sid');
                $memo = $request->input('memo');

                if ( !HourlyLog::updateMemo($sid, $memo) ) {
                    return $this->failed('Failed to update memo.');
                }

                return response()->json([
                    'success' => true,
                    'msg' => 'Successfully updated memo.'
                ]);

            case 'addManual':
                if ( Auth::user()->isAdmin() ) {
                    abort(404);
                }

                $vars = [
                    'cid', 
                    'date', 
                    'from_hour', 
                    'from_min', 
                    'to_hour', 
                    'to_min', 
                    'memo', 
                    'tz'
                ];

                foreach($vars as $v) {
                    ${$v} = $request->input($v);
                }

                $from_at = "$date ".sprintf("%02d:%02d:00", $from_hour, $from_min);
                $to_at = "$date ".sprintf("%02d:%02d:00", $to_hour, $to_min);

                $opts = [
                    'from' => $from_at,
                    'to' => $to_at,
                    'tz' => $tz,
                    'memo' => $memo
                ];

                if ( !HourlyLog::addManualSlots($cid, $opts) ) {
                    return $this->failed('Failed to add manual time.');
                }

                return response()->json([
                    'success' => true,
                    'msg' => 'Successfully added manual time.'
                ]);

            default:
                break;
        }

        return false; 
    }
}