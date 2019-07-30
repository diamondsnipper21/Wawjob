<?php namespace iJobDesk\Http\Controllers\Admin\Super\Setting;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use App;
use DB;
use Auth;
use Config;

use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\Country;

class CountryController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Countries';

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    public function index(Request $request) {
        add_breadcrumb('Countries');

        $action = $request->input('_action');

        if ($action == 'DELETE') {
            $ids = $request->input('ids');

            Country::whereIn('id', $ids)->delete();
            add_message(sprintf('The %d countries has been deleted.', count($ids)), 'success');
        // For PayPal
        } else if ( $action == 'ENABLE_PAYPAL' ) {
            $ids = $request->input('ids');

            Country::whereIn('id', $ids)->update(['paypal_enabled' => 1]);
            add_message(sprintf('The %d countries has been enabled for PayPal.', count($ids)), 'success');
        } else if ( $action == 'DISABLE_PAYPAL' ) {
            $ids = $request->input('ids');

            Country::whereIn('id', $ids)->update(['paypal_enabled' => 0]);
            add_message(sprintf('The %d countries has been disabled for PayPal.', count($ids)), 'success');
        // For Payoneer
        } else if ( $action == 'ENABLE_PAYONEER' ) {
            $ids = $request->input('ids');

            Country::whereIn('id', $ids)->update(['payoneer_enabled' => 1]);
            add_message(sprintf('The %d countries has been enabled for Payoneer.', count($ids)), 'success');
        } else if ( $action == 'DISABLE_PAYONEER' ) {
            $ids = $request->input('ids');

            Country::whereIn('id', $ids)->update(['payoneer_enabled' => 0]);
            add_message(sprintf('The %d countries has been disabled for Payoneer.', count($ids)), 'success');
        // For Skrill
        } else if ( $action == 'ENABLE_SKRILL' ) {
            $ids = $request->input('ids');

            Country::whereIn('id', $ids)->update(['skrill_enabled' => 1]);
            add_message(sprintf('The %d countries has been enabled for Skrill.', count($ids)), 'success');
        } else if ( $action == 'DISABLE_SKRILL' ) {
            $ids = $request->input('ids');

            Country::whereIn('id', $ids)->update(['skrill_enabled' => 0]);
            add_message(sprintf('The %d countries has been disabled for Skrill.', count($ids)), 'success');
        // For WeChat
        } else if ( $action == 'ENABLE_WECHAT' ) {
            $ids = $request->input('ids');

            Country::whereIn('id', $ids)->update(['wechat_enabled' => 1]);
            add_message(sprintf('The %d countries has been enabled for WeChat.', count($ids)), 'success');
        } else if ( $action == 'DISABLE_WECHAT' ) {
            $ids = $request->input('ids');

            Country::whereIn('id', $ids)->update(['wechat_enabled' => 0]);
            add_message(sprintf('The %d countries has been disabled for WeChat.', count($ids)), 'success');
        // For Bank Transfer
        } else if ( $action == 'ENABLE_BANK' ) {
            $ids = $request->input('ids');

            Country::whereIn('id', $ids)->update(['bank_enabled' => 1]);
            add_message(sprintf('The %d countries has been enabled for bank transter.', count($ids)), 'success');
        } else if ( $action == 'DISABLE_BANK' ) {
            $ids = $request->input('ids');

            Country::whereIn('id', $ids)->update(['bank_enabled' => 0]);
            add_message(sprintf('The %d countries has been disabled for bank transfer.', count($ids)), 'success');
        }

        $sort     = $request->input('sort', 'name');
        $sort_dir = $request->input('sort_dir', 'asc');

        $countries = Country::orderBy($sort, $sort_dir);

        // Filtering
        $filter = $request->input('filter');

        // By Name
        if (!empty($filter['name'])) {
            $countries->where('name', 'LIKE', '%'.trim($filter['name']).'%');
        }

        // By Code
        if (!empty($filter['charcode'])) {
            $countries->where('charcode', 'LIKE', '%'.trim($filter['charcode']).'%');
            $countries->whereRaw('LOWER(charcode)="'.trim(strtolower($filter['charcode'])).'"');
        }

        // By Code
        if (!empty($filter['country_code'])) {
            $countries->where('country_code', trim($filter['country_code']));
        }

        if (!empty($filter['sub_region'])) {
            $countries->where('sub_region', 'LIKE', '%'.trim($filter['sub_region']).'%');
        }

        if ($filter['paypal_enabled'] != '') {
            $countries->where('paypal_enabled', $filter['paypal_enabled']);
        }

        if ($filter['payoneer_enabled'] != '') {
            $countries->where('payoneer_enabled', $filter['payoneer_enabled']);
        }

        if ($filter['skrill_enabled'] != '') {
            $countries->where('skrill_enabled', $filter['skrill_enabled']);
        }

        if ($filter['wechat_enabled'] != '') {
            $countries->where('wechat_enabled', $filter['wechat_enabled']);
        }

        if ($filter['bank_enabled'] != '') {
            $countries->where('bank_enabled', $filter['bank_enabled']);
        }

        $request->flashOnly('filter');

        return view('pages.admin.super.settings.countries', [
            'page' => 'super.settings.countries',
            'countries' => $countries->paginate($this->per_page),

            'sort'   => $sort,
            'sort_dir'   => '_' . $sort_dir
        ]);
    }
}