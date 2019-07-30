<?php

use Illuminate\Database\Seeder;

class PaymentGatewaysTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('payment_gateways')->delete();
        
        \DB::table('payment_gateways')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => '{"en":"PayPal","ch":"PayPal"}',
                'type' => 1,
                'is_bank' => 0,
                'logo' => '/assets/images/pages/payment/paypal.png',
                'fields' => '',
                'sort' => 1,
                'is_active' => 1,
                'enable_withdraw' => 1,
                'created_at' => '2018-04-26 04:12:20',
                'updated_at' => '2018-04-26 04:12:20',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => '{"en":"Credit Card","ch":"信用卡"}',
                'type' => 2,
                'is_bank' => 0,
                'logo' => '/assets/images/pages/payment/creditcard.png',
                'fields' => '',
                'sort' => 2,
                'is_active' => 0,
                'enable_withdraw' => 0,
                'created_at' => '2018-04-26 04:12:20',
                'updated_at' => '2018-08-11 04:09:45',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => '{"en":"WeChat","ch":"微信支付"}',
                'type' => 3,
                'is_bank' => 0,
                'logo' => '/assets/images/pages/payment/wepay.png',
                'fields' => '',
                'sort' => 3,
                'is_active' => 1,
                'enable_withdraw' => 1,
                'created_at' => '2018-04-26 04:12:20',
                'updated_at' => '2018-08-31 12:28:17',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('payment_gateways')->insert(array (
            0 => 
            array (
                'id' => 4,
                'name' => '{"en":"Bank Transfer","ch":"银行转帐"}',
                'type' => 4,
                'is_bank' => 1,
                'logo' => '/assets/images/pages/payment/banktransfer.png',
                'fields' => '',
                'sort' => 4,
                'is_active' => 1,
                'enable_withdraw' => 0,
                'created_at' => '2018-04-26 04:12:20',
                'updated_at' => '2018-08-11 04:09:45',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 5,
                'name' => '{"en":"Skrill","ch":"Skrill"}',
                'type' => 5,
                'is_bank' => 0,
                'logo' => '/assets/images/pages/payment/skrill.png',
                'fields' => '',
                'sort' => 5,
                'is_active' => 1,
                'enable_withdraw' => 1,
                'created_at' => '2018-04-26 04:12:20',
                'updated_at' => '2018-11-14 02:58:29',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 6,
                'name' => '{"en":"Payoneer","ch":"Payoneer"}',
                'type' => 6,
                'is_bank' => 0,
                'logo' => '/assets/images/pages/payment/payoneer.png',
                'fields' => '',
                'sort' => 6,
                'is_active' => 1,
                'enable_withdraw' => 1,
                'created_at' => '2018-04-26 04:12:20',
                'updated_at' => '2018-09-06 15:18:24',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}