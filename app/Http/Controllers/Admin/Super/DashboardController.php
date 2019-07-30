<?php namespace iJobDesk\Http\Controllers\Admin\Super;
/**
 * @author KCG
 * @since July 6, 2017
 * Overview for Super Manager
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use iJobDesk\Models\User;
use iJobDesk\Models\UserNotification;
use iJobDesk\Models\Todo;
use iJobDesk\Models\Project;
use iJobDesk\Models\Contract;
use iJobDesk\Models\TransactionLocal;

class DashboardController extends BaseController {

    public function __construct() {
        $this->page_title = 'Dashboard';
        parent::__construct();
    }

    /**
    * Show ticket dashboard.
    *
    * @return Response
    */
    public function index(Request $request) {
        $todos = Todo::getOpenings($this->per_page);

        if ($request->ajax()) {
            $graph_id = $request->input('graph_id');

            ${$graph_id . '_start_date'} = strtotime($request->input('start_date'));
            ${$graph_id . '_end_date'} = strtotime($request->input('end_date'));
        }

        // User Growth
        if (empty($growth_end_date) || empty($growth_start_date)) {
            $growth_end_date   = strtotime(date('Y-m-d'));
            $growth_start_date = $growth_end_date - 31 * 24 * 3600; // a month ago   
        }

        // Job Posting Growth
        if (empty($jobposting_end_date) || empty($jobposting_start_date)) {
            $jobposting_end_date   = strtotime(date('Y-m-d'));
            $jobposting_start_date = $jobposting_end_date - 31 * 24 * 3600; // a month ago   
        }

        // Transactions
        if (empty($transaction_end_date) || empty($transaction_start_date)) {
            $transaction_end_date   = strtotime(date('Y-m-d'));
            $transaction_start_date = $transaction_end_date - 31 * 24 * 3600; // a month ago   
        }


        return view('pages.admin.super.dashboard', [
            'page' => 'super.dashboard',
            'todos' => $todos,
            'growth' => [
                'start_date'    => date('m/d/Y', $growth_start_date),
                'end_date'      => date('m/d/Y', $growth_end_date),
                'graph'         => [
                    'line'  => [
                        'data'      => $this->userGrowthGraphLineData($growth_start_date, $growth_end_date),
                        'options'   => $this->lineChartOptions(['Buyer', 'Freelancer', 'Total'])
                    ],
                    'pie'   => $this->userGrowthGraphPieData($growth_start_date, $growth_end_date)
                ]
            ],

            'jobposting' => [
                'start_date'    => date('m/d/Y', $jobposting_start_date),
                'end_date'      => date('m/d/Y', $jobposting_end_date),
                'graph'         => [
                    'line'  => [
                        'data'      => $this->jobpostingGraphLineData($jobposting_start_date, $jobposting_end_date),
                        'options'   => $this->lineChartOptions(['Fixed Job Postings', 'Hourly Job Postings', 'Constract Started', 'Constract Suspended'])
                    ],
                    'pie'   => $this->jobpostingGraphPieData($jobposting_start_date, $jobposting_end_date)
                ]
            ],

            'transaction' => [
                'start_date'    => date('m/d/Y', $transaction_start_date),
                'end_date'      => date('m/d/Y', $transaction_end_date),
                'graph'         => [
                    'line'  => [
                        'data'      => $this->transactionGraphLineData($transaction_start_date, $transaction_end_date),
                        'options'   => $this->lineChartOptions(['Fixed Pay Amount', 'Hourly Pay Amount', 'Bonus Amount', 'Total Amount', 'iJobDesk Earning Amount'])
                    ],
                    'pie'   => $this->transactionGraphPieData($transaction_start_date, $transaction_end_date)
                ]
            ]
            
        ]);
    }

    private function userGrowthGraphLineData($start_date = null, $end_date = null) {
        $graphDataList = [];

        for ($date = $start_date; $date <= $end_date; $date += 24* 3600) {
            $users = User::whereBetween('created_at', [date('Y-m-d H:i:s', $date), date('Y-m-d H:i:s', $date + 24 * 3600)])->get();
            
            $graphData = [];
            $graphData['date'] = date('Y-m-d', $date);

            foreach (['Buyer', 'Freelancer', 'Total'] as $key)
                $graphData[$key] = 0;

            foreach ($users as $user) {
                if ($user->isBuyer())
                    $graphData['Buyer']++;
                else if ($user->isFreelancer())
                    $graphData['Freelancer']++;
            }
            $graphData['Total'] = $graphData['Buyer'] + $graphData['Freelancer'];

            $graphDataList[] = $graphData;
        }

        return $graphDataList;
    }

    private function userGrowthGraphPieData($start_date = null, $end_date = null) {
        $graphDataList = [];

        if (empty($start_date)) {
            $end_date = strtotime(date('Y-m-d'));
            $start_date = $end_date - 7 * 24 * 3600; // a week ago
        }

        $users = User::whereBetween('created_at', [date('Y-m-d H:i:s', $start_date), date('Y-m-d H:i:s', $end_date + 24 * 3600)])->get();

        foreach (['Buyer', 'Freelancer'] as $key) {
            $graphDataList[$key] = ['type' => $key, 'value' => 0];
        }

        foreach ($users as $user) {
            if ($user->isBuyer())
                $graphDataList['Buyer']['value']++;
            else if ($user->isFreelancer()) {
                $graphDataList['Freelancer']['value']++;
            }
        }
        
        return array_values($graphDataList);
    }

    private function jobpostingGraphLineData($start_date = null, $end_date = null) {
        $graphDataList = [];

        for ($date = $start_date; $date <= $end_date; $date += 24* 3600) {
            $projects = Project::whereBetween('created_at', [date('Y-m-d H:i:s', $date), date('Y-m-d H:i:s', $date + 24 * 3600)])->get();
            $contracts = Contract::whereBetween('created_at', [date('Y-m-d H:i:s', $date), date('Y-m-d H:i:s', $date + 24 * 3600)])->where('status','<>',Contract::STATUS_OFFER)->get();
            
            $graphData = [];
            $graphData['date'] = date('Y-m-d', $date);

            foreach (['Fixed Job Postings', 'Hourly Job Postings', 'Constract Started', 'Constract Suspended'] as $key){
                $graphData[$key] = 0;
            }

            foreach ($projects as $project) {
                if ($project->type == Project::TYPE_FIXED)
                    $graphData['Fixed Job Postings']++;
                else if ($project->type == Project::TYPE_HOURLY)
                    $graphData['Hourly Job Postings']++;
            }

            foreach ($contracts as $contract) {
                $graphData['Constract Started']++;
                if ($contract->isSuspended()) {
                    $graphData['Constract Suspended']++;
                }
                
            }

            $graphDataList[] = $graphData;
        }

        return $graphDataList;
    }

    private function jobpostingGraphPieData($start_date = null, $end_date = null) {
        $graphDataList = [];

        if (empty($start_date)) {
            $end_date = strtotime(date('Y-m-d'));
            $start_date = $end_date - 7 * 24 * 3600; // a week ago
        }

        $projects = Project::whereBetween('created_at', [date('Y-m-d H:i:s', $start_date), date('Y-m-d H:i:s', $end_date + 24 * 3600)])->get();
        $contracts = Contract::whereBetween('created_at', [date('Y-m-d H:i:s', $start_date), date('Y-m-d H:i:s', $end_date + 24 * 3600)])->where('status','<>',Contract::STATUS_OFFER)->get();

        foreach (['Fixed Job Postings', 'Hourly Job Postings', 'Constract Started', 'Constract Suspended'] as $key) {
            $graphDataList[$key] = ['type' => $key, 'value' => 0];
        }

        foreach ($projects as $project) {
            if ($project->type == Project::TYPE_FIXED)
                $graphDataList['Fixed Job Postings']['value']++;
            else if ($project->type == Project::TYPE_HOURLY)
                $graphDataList['Hourly Job Postings']['value']++;
        }

        foreach ($contracts as $contract) {
            $graphDataList['Constract Started']['value']++;
            if ($contract->isSuspended()) {
                $graphDataList['Constract Suspended']['value']++;
            }
            
        }
        
        return array_values($graphDataList);
    }

    private function transactionGraphLineData($start_date = null, $end_date = null) {
        $graphDataList = [];

        for ($date = $start_date; $date <= $end_date; $date += 24* 3600) {
            $transactions = TransactionLocal::whereBetween('created_at', [date('Y-m-d H:i:s', $date), date('Y-m-d H:i:s', $date + 24 * 3600)])
            ->whereIn('type',[TransactionLocal::TYPE_FIXED,TransactionLocal::TYPE_HOURLY,TransactionLocal::TYPE_BONUS])
            ->where('for',TransactionLocal::FOR_BUYER)
            ->get();

            $earning_transactions = TransactionLocal::whereBetween('created_at', [date('Y-m-d H:i:s', $date), date('Y-m-d H:i:s', $date + 24 * 3600)])
            ->whereIn('type',[TransactionLocal::TYPE_FIXED,TransactionLocal::TYPE_HOURLY,TransactionLocal::TYPE_BONUS])
            ->where('for',TransactionLocal::FOR_IJOBDESK)
            ->get();
            
            $graphData = [];
            $graphData['date'] = date('Y-m-d', $date);

            foreach (['Fixed Pay Amount', 'Hourly Pay Amount', 'Bonus Amount', 'Total Amount', 'iJobDesk Earning Amount'] as $key){
                $graphData[$key] = 0;
            }

            foreach ($transactions as $transaction) {
                if ($transaction->type == TransactionLocal::TYPE_FIXED)
                    $graphData['Fixed Pay Amount'] += abs($transaction->amount);
                else if ($transaction->type == TransactionLocal::TYPE_HOURLY)
                    $graphData['Hourly Pay Amount'] += abs($transaction->amount);
                else if ($transaction->type == TransactionLocal::TYPE_BONUS)
                    $graphData['Bonus Amount'] += abs($transaction->amount);
            }

            $graphData['Total Amount'] = $graphData['Fixed Pay Amount'] + $graphData['Hourly Pay Amount'] + $graphData['Bonus Amount'];

            foreach ($earning_transactions as $earning_transaction) {
                
                $graphData['iJobDesk Earning Amount'] += $earning_transaction->amount;
            }
            
            $graphDataList[] = $graphData;
        }

        return $graphDataList;
    }

    private function transactionGraphPieData($start_date = null, $end_date = null) {
        $graphDataList = [];

        if (empty($start_date)) {
            $end_date = strtotime(date('Y-m-d'));
            $start_date = $end_date - 7 * 24 * 3600; // a week ago
        }

        $transactions = TransactionLocal::whereBetween('created_at', [date('Y-m-d H:i:s', $start_date), date('Y-m-d H:i:s', $end_date + 24 * 3600)])
            ->whereIn('type',[TransactionLocal::TYPE_FIXED,TransactionLocal::TYPE_HOURLY,TransactionLocal::TYPE_BONUS])
            ->where('for',TransactionLocal::FOR_BUYER)
            ->get();

        $earning_transactions = TransactionLocal::whereBetween('created_at', [date('Y-m-d H:i:s', $start_date), date('Y-m-d H:i:s', $end_date + 24 * 3600)])
            ->whereIn('type',[TransactionLocal::TYPE_FIXED,TransactionLocal::TYPE_HOURLY,TransactionLocal::TYPE_BONUS])
            ->where('for',TransactionLocal::FOR_IJOBDESK)
            ->get();

        foreach (['Fixed Pay Amount', 'Hourly Pay Amount', 'Bonus Amount', 'iJobDesk Earning Amount'] as $key) {
            $graphDataList[$key] = ['type' => $key, 'value' => 0];
        }

        foreach ($transactions as $transaction) {
            if ($transaction->type == TransactionLocal::TYPE_FIXED)
                $graphDataList['Fixed Pay Amount']['value'] += abs($transaction->amount);
            else if ($transaction->type == TransactionLocal::TYPE_HOURLY)
                $graphDataList['Hourly Pay Amount']['value'] += abs($transaction->amount);
            else if ($transaction->type == TransactionLocal::TYPE_BONUS)
                $graphDataList['Bonus Amount']['value'] += abs($transaction->amount);
        }

        foreach ($earning_transactions as $earning_transaction) {
            
            $graphDataList['iJobDesk Earning Amount']['value'] += $earning_transaction->amount;
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

    public function delete_notification(Request $request, $id) {
        $notification = UserNotification::findOrFail($id);
        $notification->delete();

        return response()->json(['success' => true]);
    }
}