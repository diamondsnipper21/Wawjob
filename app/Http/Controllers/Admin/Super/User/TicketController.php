<?php namespace iJobDesk\Http\Controllers\Admin\Super\User;
/**
 * @author KCG
 * @since June 20, 2017
 * Ticket Controller
 */

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\Ticket\TicketController as BaseController;
use Illuminate\Http\Request;

class TicketController extends BaseController {
	public function __construct() {
        parent::__construct();

        $this->page_title = 'Tickets';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    /**
    * tickets
    * @param $user_id The identifier of User
    *
    * @return Response
    */
    public function index_view(Request $request, $user_id, $tab = 'opening') {
        return parent::index($request, $tab, $user_id);
    }

    /**
    * ticket detail
    * @param $user_id The identifier of User
    *
    * @return Response
    */
    public function detail(Request $request, $user_id, $id = null, $sent_message = false) {
        return parent::detail($request, $id, $user_id, $sent_message);
    }

    /**
     * @author KCG
     * @since July 4, 2017
     * add comment to ticket
     */
    public function send(Request $request, $user_id, $id = null) {
        return parent::send($request, $id, $user_id);
    }

    /**
     * @author KCG
     * @since July 4, 2017
     * Solve ticket
     */
    public function solve(Request $request, $user_id = null, $id = null) {
        return parent::solve($request, $user_id, $id);
    }

    public function msg_admin(Request $request, $user_id, $id = null, $sent_message = false) {
        return parent::msg_admin($request, $id, $user_id, $sent_message);
    }
}