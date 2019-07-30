<?php namespace iJobDesk\Http\Controllers\Admin\Super;
/**
 * @author KCG
 * @since July 6, 2017
 * ToDo Listing Page
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use DB;
use Auth;
use iJobDesk\Models\User;
use iJobDesk\Models\Project;
use iJobDesk\Models\ProjectApplication;
use iJobDesk\Models\Contract;
use iJobDesk\Models\TransactionLocal;
use iJobDesk\Models\SiteWallet;
use iJobDesk\Models\Wallet;
use iJobDesk\Models\UserNotification;
use iJobDesk\Models\HourlyLogMap;
use iJobDesk\Models\UserContact;

class PaymentController extends BaseController {
	public function __construct() {
        $this->page_title = 'Overview';

        parent::__construct();
    }

    /**
    * Overview
    *
    * @return Response
    */
    public function index(Request $request) {
    	add_breadcrumb('Payments');

        $lifetime = 'false';

    	$payment_data = [];

    	$payment_data['deposit'] = TransactionLocal::totalAmountDeposits();
        $payment_data['escrow'] = TransactionLocal::totalAmountEscrows();
    	$payment_data['total_withdrawals'] = TransactionLocal::totalAmountWithdraws();
        $payment_data['withdrawal'] = TransactionLocal::totalAmountWithdrawRequests();

    	$holding = SiteWallet::holding();
    	$earning = SiteWallet::earning();

    	$payment_data['holding'] = $holding->amount;
    	$payment_data['earning'] = $earning->amount;

    	if ( $request->ajax() ) {
	    	$start_date = strtotime($request->input('start_date'));
	    	$end_date = strtotime($request->input('end_date'));
            $lifetime = $request->input('lifetime');
        }

        if ( empty($end_date) || empty($start_date) ) {
            $end_date   = strtotime(date('Y-m-d'));
            $start_date = $end_date - 31 * 24 * 3600; // a month ago   
        }

        $start_date_picker = $start_date;
        $end_date_picker = $end_date;
        
        if ( $lifetime == 'true' ) {
            $end_date   = strtotime(date('Y-m-d 23:59:59'));
            $start_date = strtotime(TransactionLocal::orderBy('id', 'asc')->first()->created_at);
        }

        return view('pages.admin.super.payment.overview', [
            'page' => 'super.payment.overview',
            'payment_data' => $payment_data,
            'payment_graph' => [
                'start_date'    => date('m/d/Y', $start_date_picker),
                'end_date'      => date('m/d/Y', $end_date_picker),
                'graph'         => [
                    'line'  => [
                        'data'      => $this->paymentGraphLineData($start_date, $end_date),
                        'options'   => $this->lineChartOptions(['Fixed', 'Hourly', 'Payment(Total)', 'iJobDesk Earning'])
                    ],
                    'pie'   => $this->paymentGraphPieData($start_date, $end_date)
                ]
            ],
            'lifetime' => $lifetime,
        ]);
    }

    private function paymentGraphLineData($start_date = null, $end_date = null) {
        $graphDataList = [];

        for ($date = $start_date; $date <= $end_date; $date += 24* 3600) {
            $graphData = [];
            $graphData['date'] = date('Y-m-d', $date);

            foreach (['Fixed', 'Hourly', 'Payment(Total)', 'iJobDesk Earning'] as $key)
                $graphData[$key] = 0;

            $graphData['Fixed'] = TransactionLocal::getAmount([
            	'from' => date('Y-m-d H:i:s', $date), 
            	'to' => date('Y-m-d H:i:s', $date + 24 * 3600),
            	'type' => TransactionLocal::TYPE_FIXED,
            	'mode' => 'payment',
            ]);

            $graphData['Hourly'] = TransactionLocal::getAmount([
            	'from' => date('Y-m-d H:i:s', $date), 
            	'to' => date('Y-m-d H:i:s', $date + 24 * 3600),
            	'type' => TransactionLocal::TYPE_HOURLY,
            	'mode' => 'payment',
            ]);

            $graphData['Payment(Total)'] = TransactionLocal::getAmount([
            	'from' => date('Y-m-d H:i:s', $date), 
            	'to' => date('Y-m-d H:i:s', $date + 24 * 3600),
                'mode' => 'payment',
            ]);

            $graphData['iJobDesk Earning'] = TransactionLocal::getEarningAmount([
            	'from' => date('Y-m-d H:i:s', $date), 
            	'to' => date('Y-m-d H:i:s', $date + 24 * 3600),
            ]);

            $graphDataList[] = $graphData;
        }

        return $graphDataList;
    }

    private function paymentGraphPieData($start_date = null, $end_date = null) {
        $graphDataList = [];

        if (empty($start_date)) {
            $end_date = strtotime(date('Y-m-d'));
            $start_date = $end_date - 7 * 24 * 3600; // a week ago
        }

        $contacts = UserContact::leftJoin('users', 'user_contacts.user_id', '=', 'users.id')
						        ->leftJoin('transactions as t', 'users.id', '=', 't.user_id')
						        ->whereBetween('t.done_at', [date('Y-m-d H:i:s', $start_date), date('Y-m-d H:i:s', $end_date + 24 * 3600)])
						        ->where(function($query) {
                                    $query->where(function($query2) {
                                                $query2->whereIn('t.type', [
                                                    TransactionLocal::TYPE_FIXED,
                                                    TransactionLocal::TYPE_HOURLY,
                                                    TransactionLocal::TYPE_BONUS,
                                                    TransactionLocal::TYPE_REFUND,
                                                    TransactionLocal::TYPE_FEATURED_JOB,
                                                ])->where('t.for', '<>', TransactionLocal::FOR_BUYER);
                                            })
                                    		->orWhereIn('t.type', [
                                    			TransactionLocal::TYPE_CHARGE,
                                    			TransactionLocal::TYPE_FEATURED_JOB
                                    		])
                                    		->orWhere(function($query3) {
												$query3->whereIn('t.type', [
													TransactionLocal::TYPE_AFFILIATE,
													TransactionLocal::TYPE_AFFILIATE_CHILD
												])->where('t.for', '<>', TransactionLocal::FOR_IJOBDESK);
											});
                                })
                                ->where('t.status', TransactionLocal::STATUS_DONE)
                                ->select('country_code')
						        ->distinct()->get();

        foreach ($contacts as $contact) {
            $graphDataList[$contact->country->name] = ['type' => $contact->country->name, 'value' => 0];
        }

        $contacts = UserContact::leftJoin('users', 'user_contacts.user_id', '=', 'users.id')
						        ->leftJoin('transactions as t', 'users.id', '=', 't.user_id')
						        ->whereBetween('t.done_at', [date('Y-m-d H:i:s', $start_date), date('Y-m-d H:i:s', $end_date + 24 * 3600)])
						        ->where(function($query) {
                                    $query->where(function($query2) {
                                                $query2->whereIn('t.type', [
                                                    TransactionLocal::TYPE_FIXED,
                                                    TransactionLocal::TYPE_HOURLY,
                                                    TransactionLocal::TYPE_BONUS,
                                                    TransactionLocal::TYPE_REFUND,
                                                    TransactionLocal::TYPE_FEATURED_JOB,
                                                ])->where('t.for', '<>', TransactionLocal::FOR_BUYER);
                                            })
                                    		->orWhereIn('t.type', [
                                    			TransactionLocal::TYPE_CHARGE,
                                    			TransactionLocal::TYPE_FEATURED_JOB
                                    		])
                                    		->orWhere(function($query3) {
												$query3->whereIn('t.type', [
													TransactionLocal::TYPE_AFFILIATE,
													TransactionLocal::TYPE_AFFILIATE_CHILD
												])->where('t.for', '<>', TransactionLocal::FOR_IJOBDESK);
											});
                                })
                                ->where('t.status', TransactionLocal::STATUS_DONE)
						        ->select('country_code', 't.*')
						        ->get();

        foreach ($contacts as $contact) {
            $graphDataList[$contact->country->name]['value'] += floatval($contact->amount);
            $graphDataList[$contact->country->name]['value'] = number_format($graphDataList[$contact->country->name]['value'], 2, '.', '');
        }
        
        return array_values($graphDataList);
    }

    private function lineChartOptions($categories) {
        foreach ($categories as $key) {
            $options[] = [
                "bullet" => "square",
                "bulletBorderAlpha" => 1,
                "bulletBorderThickness" => 1,
                "fillAlphas" => 0.3,
                // "fillColorsField" => "lineColor" . $value,
                "legendValueText" => "[[value]]",
                // "lineColorField" => "lineColor" . $value,
                "title" => $key,
                "valueField" => $key
            ];
        }

        return $options;
    }

}