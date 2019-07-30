<?php namespace iJobDesk\Http\Controllers\Admin\Super\Setting;

use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use DB;
use Auth;
use Config;

use iJobDesk\Models\PaymentGateway;
use iJobDesk\Models\Settings;
use iJobDesk\Models\User;

use iJobDesk\Models\Views\ViewUser;

class PaymentMethodController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Payment Methods';
        $user = Auth::user();

        view()->share([
            'page_title' => $this->page_title
        ]);
    }

    /**
    * Show Settings Fees page.
    *
    * @return Response
    */
    public function index(Request $request) {
        add_breadcrumb('Payment Methods');

        $user = Auth::user();
        if ( !$user->isSuper() ) {
            abort(404);
        }
        
        if ( $request->method('post') ) {
            $action = $request->input('_action');

            if ($action == 'CHANGE_STATUS') {
                $status = intval($request->input('template_action'));
                $ids = $request->input('ids');
                
                if ( $status == 0 || $status == 1 ) {
                    PaymentGateway::whereIn('id', $ids)
                     				->update(['is_active' => $status]);
                } else if ( $status == 2 || $status == 3 ) {
                    PaymentGateway::whereIn('id', $ids)
                                    ->update(['enable_withdraw' => ($status == 2 ? 0 : 1)]);
                } else {
                    PaymentGateway::whereIn('id', $ids)
                                    ->update(['enable_deposit' => ($status == 4 ? 0 : 1)]);
                }

                if ($status == 0) {
                    add_message(sprintf('The %d payment methods has been disabled.', count($ids)), 'success');
                } else if ($status == 1) {
                	add_message(sprintf('The %d payment methods has been enabled.', count($ids)), 'success');
                } else if ($status == 2) {
                    add_message(sprintf('The %d payment methods has been disabled withdrawal.', count($ids)), 'success');
                } else if ($status == 3) {
                    add_message(sprintf('The %d payment methods has been eanbled withdrawal.', count($ids)), 'success');
                } else if ($status == 4) {
                    add_message(sprintf('The %d payment methods has been disabled deposit.', count($ids)), 'success');
                } else {
                    add_message(sprintf('The %d payment methods has been eanbled deposit.', count($ids)), 'success');
                }
            } else if ( $action == 'SAVE' ) {
                $type = $request->input('_type');

                $keys = [];

                switch ( $type ) {
                	case PaymentGateway::GATEWAY_PAYPAL:
                		$keys = [
                			'paypal_mode',
		                    'paypal_email',
		                    'paypal_app_id',
		                    'paypal_api_username',
		                    'paypal_api_password',
		                    'paypal_api_signature',
		                ];

                		break;
                	case PaymentGateway::GATEWAY_CREDITCARD:
                		break;
                	case PaymentGateway::GATEWAY_WEIXIN:
                		$keys = [
		                    'weixin_phone_number',
		                ];

                		break;
                    case PaymentGateway::GATEWAY_PAYONEER:
                        $keys = [
                            'payoneer_email',
                        ];

                        break;
                	case PaymentGateway::GATEWAY_SKRILL:
                		$keys = [
		                    'skrill_merchant_email',
		                    'skrill_merchant_id',
		                    'skrill_merchant_password',
		                    'skrill_merchant_secret_word',
		                ];

                		break;
                	case PaymentGateway::GATEWAY_WIRETRANSFER:
                		$keys = [
		                    'bank_name',
		                    'bank_address',
		                    'bank_account_number',
		                    'bank_routing_number',
                            'bank_name_euro',
		                    'bank_bic',
                            'bank_iban',
		                    'bank_reference',
		                    'bank_reference_user',
		                ];

                		break;
                	default:
                		break;
                }

                $updated = false;
                if ( $keys ) {
	                $updated = true;
	                foreach ( $keys as $key ) {
	                	if ( $key == 'paypal_mode' ) {
	                		if ( isset($request->paypal_mode) ) {
	                			$value = 1;
	                		} else {
	                			$value = 0;
	                		}
	                	} else {
		                    if ( !$request->input($key) ) {
		                        continue;
		                    }

		                    $value = $request->input($key);
		                }

	                    if ( !Settings::updateSetting(strtoupper($key), $value) ) {
	                        $updated = false;
	                    }
	                }
	            }

                if ( $updated ) {
                    add_message('Your settings has been updated successfully.', 'success');
                } else {
                    add_message('Your settings has not been updated successfully.', 'danger');
                }
            }
        }

        $sort     = $request->input('sort', 'id');
        $sort_dir = $request->input('sort_dir', 'asc');

        $payment_methods = PaymentGateway::orderByRaw('IF(is_active = 1, 1, 0) DESC')
                                        ->orderBy($sort, $sort_dir)
                                        ->orderBy('name', 'asc');

        // Filtering
        $filter = $request->input('filter');

        // By Name
        if (!empty($filter['name'])) {
            $payment_methods->where('name', 'LIKE', '%'.trim($filter['name']).'%');
        }

        // By Status
        if ($filter['status'] != '') {
            $payment_methods->where('is_active', $filter['status']);
        }

        // By Withdraw Status
        if ($filter['enable_withdraw'] != '') {
            $payment_methods->where('enable_withdraw', $filter['enable_withdraw']);
        }

        // By Deposit Status
        if ($filter['enable_deposit'] != '') {
            $payment_methods->where('enable_deposit', $filter['enable_deposit']);
        }

        $request->flashOnly('filter');

        return view('pages.admin.super.settings.payment_methods', [
            'page' => 'super.settings.payment_methods',
            'payment_methods' => $payment_methods->paginate($this->per_page),
            'sort' => $sort,
            'sort_dir' => '_' . $sort_dir,
        ]);
    }
}