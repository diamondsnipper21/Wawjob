<?php namespace iJobDesk\Http\Controllers\Admin\Super\Setting;
/**
 * @author PYH
 * @since December 26, 2017
 * Settings Fees Page
 */
use iJobDesk\Http\Requests;
use iJobDesk\Http\Controllers\Admin\AdminController as BaseController;
use Illuminate\Http\Request;

use DB;
use Auth;
use Config;
use iJobDesk\Models\User;
use iJobDesk\Models\Views\ViewUser;
use iJobDesk\Models\Settings;
use iJobDesk\Models\Notification;
use iJobDesk\Models\EmailTemplate;

class FeeController extends BaseController {

    public function __construct() {
        parent::__construct();

        $this->page_title = 'Fees';

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
        add_breadcrumb('Fees');

        $user = Auth::user();
    	if ( !$user->isSuper() ) {
    		abort(404);
    	}
    	
        if ( $request->method('post') ) {
            $action = $request->input('_action');

            if ( $action == 'SAVE' ) {
                $keys = [
                    'fee_rate',
                    'fee_rate_affiliated',
                    'featured_job_fee',
                    'affiliate_buyer_fee',
                    'affiliate_child_buyer_fee',
                    'affiliate_freelancer_fee_rate',
                    'affiliate_child_freelancer_fee_rate',
                    'connections_featured_project',
                    'cny_exchange_rate',
                    'cny_exchange_rate_sell',
                    'eur_exchange_rate_sell',
                    'deposit_fee_paypal',
                    'deposit_fee_skrill',
                    'deposit_fee_payoneer',
                    'withdraw_bank_fee',
                    'withdraw_paypal_fee',
                    'withdraw_paypal_fixed_fee',
                    'withdraw_payoneer_fee',
                    'withdraw_payoneer_fixed_fee',
                    'withdraw_skrill_fee',
                    'withdraw_skrill_fixed_fee',
                    'withdraw_wechat_fee',
                    'withdraw_wechat_fixed_fee',
                    'withdraw_creditcard_fee',
                    'withdraw_creditcard_fixed_fee',
                ];

                $updated = true;
                foreach ( $keys as $key ) {
                    if ( $request->input($key) == '' ) {
                        continue;
                    }

                    if ( !Settings::updateSetting(strtoupper($key), $request->input($key)) ) {
                        $updated = false;
                    }
                }

                if ( $updated ) {
                    Notification::sendToSuperAdmin('FEE_CHANGED', SUPERADMIN_ID);

                    $table = '<table width="100%">';

                    $table .= '<tr>';
                        $table .= '<td>Fee for all types of Contracts</td>';
                        $table .= '<td><strong>' . Settings::get('FEE_RATE') . '%</strong></td>';
                    $table .= '</tr>';

                    $table .= '<tr>';
                        $table .= '<td>Fee for all types of affiliated Contracts</td>';
                        $table .= '<td><strong>' . Settings::get('FEE_RATE_AFFILIATED') . '%</strong></td>';
                    $table .= '</tr>';

                    $table .= '<tr>';
                        $table .= '<td>Fee for Featured Job Posting</td>';
                        $table .= '<td><strong>$' . Settings::get('FEATURED_JOB_FEE') . '</strong></td>';
                    $table .= '</tr>';

                    $table .= '<tr>';
                        $table .= '<td>Commission for 1st Affiliate - Freelancer</td>';
                        $table .= '<td><strong>' . Settings::get('AFFILIATE_FREELANCER_FEE_RATE') . '%</strong></td>';
                    $table .= '</tr>';

                    $table .= '<tr>';
                        $table .= '<td>Commission for 2nd Affiliate - Freelancer</td>';
                        $table .= '<td><strong>' . Settings::get('AFFILIATE_CHILD_FREELANCER_FEE_RATE') . '%</strong></td>';
                    $table .= '</tr>';

                    $table .= '<tr>';
                        $table .= '<td>CNY Exchange Rate - Buy (When deposit)</td>';
                        $table .= '<td><strong>CNY ' . Settings::get('CNY_EXCHANGE_RATE') . '</strong></td>';
                    $table .= '</tr>';

                    $table .= '<tr>';
                        $table .= '<td>CNY Exchange Rate - Sell (When withdrawal)</td>';
                        $table .= '<td><strong>CNY ' . Settings::get('CNY_EXCHANGE_RATE_SELL') . '</strong></td>';
                    $table .= '</tr>';

                    $table .= '<tr>';
                        $table .= '<td>EUR Exchange Rate - Sell (When withdrawal)</td>';
                        $table .= '<td><strong>EUR ' . Settings::get('EUR_EXCHANGE_RATE_SELL') . '</strong></td>';
                    $table .= '</tr>';

                    $table .= '<tr>';
                        $table .= '<td>PayPal Deposit Transaction Fee</td>';
                        $table .= '<td><strong>' . Settings::get('DEPOSIT_FEE_PAYPAL') . '%</strong></td>';
                    $table .= '</tr>';

                    $table .= '<tr>';
                        $table .= '<td>Skrill Deposit Transaction Fee</td>';
                        $table .= '<td><strong>' . Settings::get('DEPOSIT_FEE_SKRILL') . '%</strong></td>';
                    $table .= '</tr>';

                    $table .= '<tr>';
                        $table .= '<td>Payoneer Deposit Transaction Fee</td>';
                        $table .= '<td><strong>' . Settings::get('DEPOSIT_FEE_PAYONEER') . '%</strong></td>';
                    $table .= '</tr>';

                    $table .= '<tr>';
                        $table .= '<td>Bank Transfer Withdraw Transaction Fee</td>';
                        $table .= '<td><strong>$' . Settings::get('WITHDRAW_BANK_FEE') . '</strong></td>';
                    $table .= '</tr>';

                    $table .= '<tr>';
                        $table .= '<td>PayPal Withdraw Transaction Fee</td>';
                        $table .= '<td><strong>$' . Settings::get('WITHDRAW_PAYPAL_FIXED_FEE') . ' + %' . Settings::get('WITHDRAW_PAYPAL_FEE') . '</strong></td>';
                    $table .= '</tr>';

                    $table .= '<tr>';
                        $table .= '<td>Skrill Withdraw Transaction Fee</td>';
                        $table .= '<td><strong>$' . Settings::get('WITHDRAW_SKRILL_FIXED_FEE') . ' + %' . Settings::get('WITHDRAW_SKRILL_FEE') . '</strong></td>';
                    $table .= '</tr>';

                    $table .= '<tr>';
                        $table .= '<td>Payoneer Withdraw Transaction Fee</td>';
                        $table .= '<td><strong>$' . Settings::get('WITHDRAW_PAYONEER_FIXED_FEE') . ' + %' . Settings::get('WITHDRAW_PAYONEER_FEE') . '</strong></td>';
                    $table .= '</tr>';

                    $table .= '<tr>';
                        $table .= '<td>WeChat Withdraw Transaction Fee</td>';
                        $table .= '<td><strong>$' . Settings::get('WITHDRAW_WECHAT_FIXED_FEE') . ' + %' . Settings::get('WITHDRAW_WECHAT_FEE') . '</strong></td>';
                    $table .= '</tr>';

                    $table .= '<tr>';
                        $table .= '<td>Credit Card Withdraw Transaction Fee</td>';
                        $table .= '<td><strong>$' . Settings::get('WITHDRAW_CREDITCARD_FIXED_FEE') . ' + %' . Settings::get('WITHDRAW_CREDITCARD_FEE') . '</strong></td>';
                    $table .= '</tr>';

                    $table .= '</table>';

                    EmailTemplate::sendToSuperAdmin('FEE_CHANGED', User::ROLE_USER_SUPER_ADMIN, [
                        'data' => $table,
                    ]);

                    add_message('Your settings has been updated successfully.', 'success');
                } else {
                    add_message('Your settings has not been updated successfully.', 'danger');
                }
            }
        }

        return view('pages.admin.super.settings.fees', [
            'page' => 'super.settings.fees'
        ]);
    }

    public function refresh_chinese_money_rate(Request $request) {
        $params = $request->params;

        $params = explode(',', $params);
        $type   = $params[0];
        $offset = intval($params[1]);

        $html = file_get_contents('http://www.boc.cn/sourcedb/whpj/index.html');
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $xpath = new \DomXPath($doc);

        $elements = $xpath->query("//table//td/text()[. = '美元']/../../td/text()");

        $max = 0;
        $min = 99999999;
        foreach ($elements as $i => $element) {
            if ($i < 1 || $i > 5)
                continue;

            $max = max($max, $element->nodeValue);
            $min = min($min, $element->nodeValue);
        }

        $rate = '';
        if ($type == 'max') {
            $rate = $max;
        } elseif ($type == 'min') {
            $rate = $min;
        }

        $rate += $offset;

        return response()->json(['rate' => number_format($rate / 100, 4)]);
    }
}