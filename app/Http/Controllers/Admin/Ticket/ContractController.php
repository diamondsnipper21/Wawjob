<?php namespace iJobDesk\Http\Controllers\Admin\Ticket;
/**
 * @author KCG
 * @since Feb 2, 2018
 * Contracts Page for Freelancer on super admin
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\Super\ContractController as BaseController;
use Illuminate\Http\Request;

use iJobDesk\Http\Controllers\ContractController as Controller;

class ContractController extends BaseController {

    public function __construct() {
        parent::__construct();
    }
}