<?php namespace iJobDesk\Http\Controllers\Admin\Ticket;
/**
 * @author KCG
 * @since June 9, 2017
 * Overview for Ticket Manager
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use DB;
use Auth;
use iJobDesk\Models\User;
use iJobDesk\Models\Ticket;
use iJobDesk\Models\UserNotification;

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
        if ( $request->ajax() ) {
            $start_date = strtotime($request->input('start_date'));
            $end_date = strtotime($request->input('end_date'));

            return response()->json([
                'line' => [
                    'data' => $this->ticketGraphLineData($start_date, $end_date),
                    'options' => Ticket::lineChartOptions()
                ],
                'pie' => $this->ticketGraphPieData($start_date, $end_date)
            ]);
        }

        $end_date = strtotime(date('Y-m-d'));
        $start_date = $end_date - 7 * 24 * 3600; // a week ago

        return view('pages.admin.ticket.dashboard', [
            'page' => 'ticket.dashboard',
            'tickets'       => Ticket::ownTickets($this->per_page),
            'start_date'    => date('m/d/Y', $start_date),
            'end_date'      => date('m/d/Y', $end_date),
            'stats'         => [
                                    'line' => [
                                        'data' => $this->ticketGraphLineData($start_date, $end_date),
                                        'options' => Ticket::lineChartOptions()
                                    ],
                                    'pie' => $this->ticketGraphPieData($start_date, $end_date)
                                ]
        ]);
    }

    /**
     * @author KCG
     * @since June 22, 2017
     * The data for line graph
     */
    private function ticketGraphLineData($start_date = null, $end_date = null) {
        $me = Auth::user();

        $graphDataList = [];

        $date_count = ($end_date - $start_date)  / (24 * 3600);
        $show_data_type = 'Day';
        if ($date_count > 30 && $date_count <= 365) { // By Month
            $show_data_type = 'Month';
        } elseif ($date_count > 365) { // By Year
            $show_data_type = 'Year';
        }

        for ($date = $start_date; $date <= $end_date; $date += 24* 3600) {
            $tickets = Ticket::whereBetween('created_at', [date('Y-m-d H:i:s', $date), date('Y-m-d H:i:s', $date + 24 * 3600)]);

            if (!$me->isSuper())
                $tickets->where('type', '<>', Ticket::TYPE_ID_VERIFICATION);

            $tickets = $tickets->get();

            $current_date = date('Y-n-j', $date);
            if ($show_data_type == 'Year')
                $current_date = date('Y', $date);
            elseif ($show_data_type == 'Month')
                $current_date = date('Y-n', $date);

            $graphData = [];
            if (array_key_exists($current_date, $graphDataList)) {
                $graphData = $graphDataList[$current_date];
            }

            if (empty($graphData)) {
                $graphData['date'] = $current_date;

                if ($show_data_type == 'Day') {
                    $graphData['date'] = date('n/j', $date);
                    foreach ($graphDataList as $k_date => $gd) {
                        if (date('Y-m', strtotime($k_date)) == date('Y-m', $date)) {
                            $graphData['date'] = date('j', $date);
                        }
                    }
                }

                foreach (Ticket::getOptions('type') as $key => $value) {
                    $graphData[$key] = 0;
                    $graphData['lineColor' . $value] = Ticket::colorByType($value);
                }
            }

            foreach ($tickets as $ticket) {
                foreach (Ticket::getOptions('type') as $key => $value) {
                    if (empty($graphData[$key]))
                        $graphData[$key] = 0;

                    if ($ticket->type == $value) {
                        $graphData[$key]++;
                        break;
                    }
                }
            }

            $graphDataList[$current_date] = $graphData;
        }

        return array_values($graphDataList);
    }

    /**
     * @author KCG
     * @since June 22, 2017
     * The data for pie graph
     */
    private function ticketGraphPieData($start_date = null, $end_date = null) {
        $me = Auth::user();

        $graphDataList = [];

        if (empty($start_date)) {
            $end_date = strtotime(date('Y-m-d'));
            $start_date = $end_date - 7 * 24 * 3600; // a week ago
        }

        $tickets = Ticket::whereBetween('created_at', [date('Y-m-d H:i:s', $start_date), date('Y-m-d H:i:s', $end_date + 24 * 3600)]);

        if (!$me->isSuper())
            $tickets->where('type', '<>', Ticket::TYPE_ID_VERIFICATION);

        $tickets = $tickets->get();

        foreach (Ticket::getOptions('type') as $key => $value) {
            $graphDataList[$value] = ['type' => $key, 'value' => 0];
        }

        foreach ($tickets as $ticket) {
            $graphDataList[$ticket->type]['value']++;
        }
        
        return array_values($graphDataList);
    }

    public function delete_notification(Request $request, $id) {
        $notification = UserNotification::findOrFail($id);
        $notification->delete();

        return response()->json(['success' => true]);
    }
}