<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('settings')->delete();
        
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'key' => 'FEE_RATE',
                'value' => '2.0',
                'description' => NULL,
                'created_at' => '2018-02-04 02:48:03',
                'updated_at' => '2018-11-25 02:47:10',
            ),
            1 => 
            array (
                'id' => 6,
                'key' => 'DAYS_PROCESS_PENDING_TRANSACTION',
                'value' => '3',
                'description' => NULL,
                'created_at' => '2018-01-28 03:22:12',
                'updated_at' => '2018-01-28 03:22:12',
            ),
            2 => 
            array (
                'id' => 7,
                'key' => 'DAYS_PROJECT_EXPIRED',
                'value' => '100',
                'description' => NULL,
                'created_at' => '2018-01-26 08:33:02',
                'updated_at' => NULL,
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 8,
                'key' => 'DAYS_AVAILABLE_REFUND',
                'value' => '7',
                'description' => NULL,
                'created_at' => '2018-01-27 02:35:26',
                'updated_at' => '2018-01-27 02:35:26',
            ),
            1 => 
            array (
                'id' => 9,
                'key' => 'DAYS_AVAILABLE_DISPUTE',
                'value' => '7',
                'description' => NULL,
                'created_at' => '2018-01-27 02:35:31',
                'updated_at' => '2018-01-27 02:35:31',
            ),
            2 => 
            array (
                'id' => 10,
                'key' => 'DAYS_AVAILABLE_PAYMENT_METHOD',
                'value' => '0',
                'description' => NULL,
                'created_at' => '2018-01-28 03:21:31',
                'updated_at' => NULL,
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 15,
                'key' => 'CNY_EXCHANGE_RATE',
                'value' => '7.0089',
                'description' => NULL,
                'created_at' => '2018-01-29 22:59:21',
                'updated_at' => '2018-11-30 06:18:38',
            ),
            1 => 
            array (
                'id' => 16,
                'key' => 'WITHDRAW_FEE',
                'value' => '0',
                'description' => NULL,
                'created_at' => '2018-06-06 00:15:19',
                'updated_at' => '2018-06-06 00:15:19',
            ),
            2 => 
            array (
                'id' => 17,
                'key' => 'WITHDRAW_MAX_AMOUNT',
                'value' => '10000.00',
                'description' => NULL,
                'created_at' => '2018-05-04 06:47:00',
                'updated_at' => '2018-05-04 06:47:00',
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 18,
                'key' => 'WITHDRAW_MIN_AMOUNT',
                'value' => '1.1',
                'description' => NULL,
                'created_at' => '2018-01-30 07:18:59',
                'updated_at' => '2018-01-30 07:18:59',
            ),
            1 => 
            array (
                'id' => 19,
                'key' => 'DAYS_RESET_CONNECTIONS',
                'value' => '7',
                'description' => NULL,
                'created_at' => '2018-01-30 06:19:51',
                'updated_at' => '2018-01-30 06:19:51',
            ),
            2 => 
            array (
                'id' => 20,
                'key' => 'TOTAL_CONNECTIONS_RESET',
                'value' => '30',
                'description' => NULL,
                'created_at' => '2018-01-30 06:22:05',
                'updated_at' => NULL,
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 21,
                'key' => 'AFFILIATE_BUYER_FEE',
                'value' => '3.9',
                'description' => 'Get paid 3.9% of the total amount that affiliated buyer pays',
                'created_at' => '2018-02-02 06:02:50',
                'updated_at' => '2018-02-01 22:02:50',
            ),
            1 => 
            array (
                'id' => 22,
                'key' => 'AFFILIATE_CHILD_BUYER_FEE',
                'value' => '0.39',
                'description' => 'Get paid 0.39% of the total amount that child affiliated buyer pays',
                'created_at' => '2018-02-02 06:02:50',
                'updated_at' => '2018-02-01 22:02:50',
            ),
            2 => 
            array (
                'id' => 23,
                'key' => 'AFFILIATE_FREELANCER_FEE_RATE',
                'value' => '1',
                'description' => 'Get paid 1% of the total amount that affiliated freelancer gets',
                'created_at' => '2018-02-02 06:02:50',
                'updated_at' => '2018-02-01 22:02:50',
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 24,
                'key' => 'AFFILIATE_CHILD_FREELANCER_FEE_RATE',
                'value' => '0.1',
                'description' => 'Get paid 0.1% of the total amount that child affiliated freelancer gets',
                'created_at' => '2018-02-04 02:47:55',
                'updated_at' => '2018-02-03 18:47:55',
            ),
            1 => 
            array (
                'id' => 28,
                'key' => 'FEATURED_JOB_FEE',
                'value' => '20',
                'description' => NULL,
                'created_at' => '2018-02-02 06:02:50',
                'updated_at' => '2018-02-01 22:02:50',
            ),
            2 => 
            array (
                'id' => 29,
                'key' => 'CONNECTIONS_FEATURED_PROJECT',
                'value' => '2',
                'description' => NULL,
                'created_at' => '2018-02-04 03:02:01',
                'updated_at' => '2018-02-03 19:02:01',
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 30,
                'key' => 'CONTACT_EMAIL_ADDRESS',
                'value' => 'support@ijobdesk.com',
                'description' => NULL,
                'created_at' => '2018-02-20 19:50:35',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 31,
                'key' => 'BANK_ACCOUNT_NAME',
                'value' => 'iJobDesk',
                'description' => NULL,
                'created_at' => '2018-05-05 05:50:53',
                'updated_at' => '2018-05-04 21:50:53',
            ),
            2 => 
            array (
                'id' => 32,
                'key' => 'BANK_NAME',
                'value' => 'iJobDesk LTD',
                'description' => NULL,
                'created_at' => '2018-05-05 05:50:53',
                'updated_at' => '2018-05-04 21:50:53',
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 33,
                'key' => 'BANK_BRANCH_ADDRESS',
                'value' => 'No. 40 Taiyuan St., Shenyang, China',
                'description' => NULL,
                'created_at' => '2018-05-05 05:50:53',
                'updated_at' => '2018-05-04 21:50:53',
            ),
            1 => 
            array (
                'id' => 34,
                'key' => 'BANK_ACCOUNT_NUMBER',
                'value' => '15588889999',
                'description' => NULL,
                'created_at' => '2018-05-05 05:50:53',
                'updated_at' => '2018-05-04 21:50:53',
            ),
            2 => 
            array (
                'id' => 35,
                'key' => 'BANK_ROUTING_NUMBER',
                'value' => '99998888551',
                'description' => NULL,
                'created_at' => '2018-05-05 05:50:53',
                'updated_at' => '2018-05-04 21:50:53',
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 36,
                'key' => 'BANK_SWIFT_CODE',
                'value' => 'SPDBLASY',
                'description' => NULL,
                'created_at' => '2018-05-05 05:50:53',
                'updated_at' => '2018-05-04 21:50:53',
            ),
            1 => 
            array (
                'id' => 37,
                'key' => 'BANK_REFERENCE',
                'value' => 'iJobDesk',
                'description' => NULL,
                'created_at' => '2018-05-29 03:01:48',
                'updated_at' => '2018-05-28 19:01:48',
            ),
            2 => 
            array (
                'id' => 38,
                'key' => 'BANK_REFERENCE_USER',
                'value' => 'iJobDesk',
                'description' => NULL,
                'created_at' => '2018-05-04 19:11:08',
                'updated_at' => '2018-05-04 11:11:08',
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 39,
                'key' => 'PAYPAL_EMAIL',
                'value' => 'i4usoft4989@gmail.com',
                'description' => NULL,
                'created_at' => '2018-05-29 02:16:40',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 40,
                'key' => 'SKRILL_MERCHANT_EMAIL',
                'value' => 'i4usoft4989@gmail.com',
                'description' => NULL,
                'created_at' => '2018-05-29 02:30:06',
                'updated_at' => '2018-08-15 06:10:28',
            ),
            2 => 
            array (
                'id' => 41,
                'key' => 'WEIXIN_PHONE_NUMBER',
                'value' => '+86 18698703415',
                'description' => NULL,
                'created_at' => '2018-05-29 03:01:04',
                'updated_at' => '2018-08-15 06:12:33',
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 42,
                'key' => 'PAYPAL_API_USERNAME',
                'value' => 'i4usoft4989_api2.gmail.com',
                'description' => NULL,
                'created_at' => '2018-05-29 02:29:15',
                'updated_at' => '2018-05-29 02:29:15',
            ),
            1 => 
            array (
                'id' => 43,
                'key' => 'PAYPAL_API_PASSWORD',
                'value' => '6DEVH3CZVWXSNPJX',
                'description' => NULL,
                'created_at' => '2018-05-29 02:29:03',
                'updated_at' => '2018-05-29 02:29:03',
            ),
            2 => 
            array (
                'id' => 44,
                'key' => 'PAYPAL_API_SIGNATURE',
                'value' => 'AIniTnB2DmuVSv7UmuNhQlEUGQA4AM3IvvVou68U4.oGvwXcyHn5RBs0',
                'description' => NULL,
                'created_at' => '2018-05-29 02:28:54',
                'updated_at' => '2018-05-29 02:28:54',
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 45,
                'key' => 'PAYPAL_APP_ID',
                'value' => 'APP-80W284485P519543T',
                'description' => NULL,
                'created_at' => '2018-05-29 02:28:49',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 46,
                'key' => 'SKRILL_MERCHANT_PASSWORD',
                'value' => 'skrill123',
                'description' => NULL,
                'created_at' => '2018-05-29 02:29:48',
                'updated_at' => '2018-05-29 02:29:48',
            ),
            2 => 
            array (
                'id' => 47,
                'key' => 'SKRILL_MERCHANT_SECRET_WORD',
                'value' => 'skrill',
                'description' => NULL,
                'created_at' => '2018-05-29 02:31:27',
                'updated_at' => NULL,
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 48,
                'key' => 'SKRILL_MERCHANT_ID',
                'value' => '123',
                'description' => NULL,
                'created_at' => '2018-05-29 02:32:06',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 49,
                'key' => 'PAYPAL_MODE',
                'value' => '1',
                'description' => NULL,
                'created_at' => '2018-05-29 09:55:01',
                'updated_at' => '2018-05-29 01:55:01',
            ),
            2 => 
            array (
                'id' => 50,
                'key' => 'CURRENCY',
                'value' => 'USD',
                'description' => NULL,
                'created_at' => '2018-05-29 21:13:55',
                'updated_at' => NULL,
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 51,
                'key' => 'FEE_RATE_AFFILIATED',
                'value' => '1.1',
                'description' => NULL,
                'created_at' => '2018-06-21 12:50:03',
                'updated_at' => '2018-08-25 08:58:17',
            ),
            1 => 
            array (
                'id' => 52,
                'key' => 'WITHDRAW_BANK_FEE',
                'value' => '35',
                'description' => 'Fixed $30',
                'created_at' => '2018-02-02 06:02:50',
                'updated_at' => '2018-11-30 06:18:38',
            ),
            2 => 
            array (
                'id' => 53,
                'key' => 'WITHDRAW_PAYPAL_FEE',
                'value' => '4.4',
                'description' => '4% Fee',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 54,
                'key' => 'PAYONEER_EMAIL',
                'value' => 'jinorypayment@yahoo.com',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => '2018-08-15 06:39:01',
            ),
            1 => 
            array (
                'id' => 55,
                'key' => 'CNY_EXCHANGE_RATE_SELL',
                'value' => '6.8232',
                'description' => NULL,
                'created_at' => '2018-01-29 22:59:21',
                'updated_at' => '2018-11-30 06:18:38',
            ),
            2 => 
            array (
                'id' => 56,
                'key' => 'POINT_PORTRAIT',
                'value' => '20',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => '2018-08-31 10:12:08',
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 57,
                'key' => 'POINT_PORTRAIT_ENABLED',
                'value' => '1',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 58,
                'key' => 'POINT_PORTFOLIO',
                'value' => '10',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => '2018-08-31 10:12:08',
            ),
            2 => 
            array (
                'id' => 59,
                'key' => 'POINT_PORTFOLIO_ENABLED',
                'value' => '1',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 60,
                'key' => 'POINT_CERTIFICATION',
                'value' => '5',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => '2018-08-31 10:12:08',
            ),
            1 => 
            array (
                'id' => 61,
                'key' => 'POINT_CERTIFICATION_ENABLED',
                'value' => '1',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 62,
                'key' => 'POINT_EMPLOYMENT_HISTORY',
                'value' => '10',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 63,
                'key' => 'POINT_EMPLOYMENT_HISTORY_ENABLED',
                'value' => '1',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 64,
                'key' => 'POINT_EDUCATION',
                'value' => '5',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => '2018-08-31 10:12:08',
            ),
            2 => 
            array (
                'id' => 65,
                'key' => 'POINT_EDUCATION_ENABLED',
                'value' => '1',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 66,
                'key' => 'POINT_ID_VERIFIED',
                'value' => '20',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => '2018-08-31 10:12:08',
            ),
            1 => 
            array (
                'id' => 67,
                'key' => 'POINT_ID_VERIFIED_ENABLED',
                'value' => '1',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 68,
                'key' => 'POINT_NEW_FREELANCER',
                'value' => '5',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => '2018-08-31 10:12:08',
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 69,
                'key' => 'POINT_NEW_FREELANCER_ENABLED',
                'value' => '1',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 70,
                'key' => 'POINT_JOB_SUCCESS',
                'value' => '200',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 71,
                'key' => 'POINT_JOB_SUCCESS_ENABLED',
                'value' => '1',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 72,
                'key' => 'POINT_OPEN_JOBS',
                'value' => '0.25',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => '2018-09-03 16:54:53',
            ),
            1 => 
            array (
                'id' => 73,
                'key' => 'POINT_OPEN_JOBS_ENABLED',
                'value' => '1',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 74,
                'key' => 'POINT_LAST_12MONTHS',
                'value' => '2',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => '2018-09-04 10:39:02',
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 75,
                'key' => 'POINT_LAST_12MONTHS_ENABLED',
                'value' => '1',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 76,
                'key' => 'POINT_LIFETIME',
                'value' => '1',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 77,
                'key' => 'POINT_LIFETIME_ENABLED',
                'value' => '1',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 78,
                'key' => 'POINT_SCORE',
                'value' => '200',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 79,
                'key' => 'POINT_SCORE_ENABLED',
                'value' => '1',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 80,
                'key' => 'POINT_ACTIVITY',
                'value' => '10',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 81,
                'key' => 'POINT_ACTIVITY_ENABLED',
                'value' => '1',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 82,
                'key' => 'POINT_DISPUTE',
                'value' => '-10',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 83,
                'key' => 'POINT_DISPUTE_ENABLED',
                'value' => '1',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 84,
                'key' => 'DEPOSIT_FEE_PAYPAL',
                'value' => '0',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => '2018-11-01 13:46:09',
            ),
            1 => 
            array (
                'id' => 85,
                'key' => 'DEPOSIT_FEE_SKRILL',
                'value' => '0',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => '2018-11-01 13:46:09',
            ),
            2 => 
            array (
                'id' => 86,
                'key' => 'DEPOSIT_FEE_PAYONEER',
                'value' => '0',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => '2018-10-08 17:37:20',
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 87,
                'key' => 'COMPANY_ADDRESS',
                'value' => 'Tina tn 21-5, Kesklinna linnaosa, Harju maakond, Tallinn 10126, Estonia',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 88,
                'key' => 'POINT_SCORE_PER_DOLLAR',
                'value' => '0.5',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 89,
                'key' => 'POINT_SCORE_NON_REVIEW',
                'value' => '2.5',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 90,
                'key' => 'WITHDRAW_PAYPAL_FIXED_FEE',
                'value' => '20',
                'description' => '$10 Fee',
                'created_at' => NULL,
                'updated_at' => '2018-12-03 07:22:09',
            ),
            1 => 
            array (
                'id' => 91,
                'key' => 'WITHDRAW_SKRILL_FEE',
                'value' => '4.8',
                'description' => '4% Fee',
                'created_at' => NULL,
                'updated_at' => '2018-11-30 06:19:10',
            ),
            2 => 
            array (
                'id' => 92,
                'key' => 'WITHDRAW_SKRILL_FIXED_FEE',
                'value' => '1',
                'description' => '$10 Fee',
                'created_at' => NULL,
                'updated_at' => '2018-12-03 07:28:29',
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 93,
                'key' => 'WITHDRAW_PAYONEER_FEE',
                'value' => '4.8',
                'description' => '4% Fee',
                'created_at' => NULL,
                'updated_at' => '2018-11-30 06:19:10',
            ),
            1 => 
            array (
                'id' => 94,
                'key' => 'WITHDRAW_PAYONEER_FIXED_FEE',
                'value' => '1',
                'description' => '$10 Fee',
                'created_at' => NULL,
                'updated_at' => '2018-12-03 07:28:29',
            ),
            2 => 
            array (
                'id' => 95,
                'key' => 'APP_VERSION',
                'value' => '1.1.0.1015',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 96,
                'key' => 'WITHDRAW_WECHAT_FEE',
                'value' => '2',
                'description' => '4% Fee',
                'created_at' => NULL,
                'updated_at' => '2018-12-03 07:22:09',
            ),
            1 => 
            array (
                'id' => 97,
                'key' => 'WITHDRAW_WECHAT_FIXED_FEE',
                'value' => '1',
                'description' => '$10 Fee',
                'created_at' => NULL,
                'updated_at' => '2018-12-03 07:28:29',
            ),
            2 => 
            array (
                'id' => 98,
                'key' => 'WITHDRAW_CREDITCARD_FEE',
                'value' => '0',
                'description' => '4% Fee',
                'created_at' => NULL,
                'updated_at' => '2018-11-30 06:19:10',
            ),
        ));
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'id' => 99,
                'key' => 'WITHDRAW_CREDITCARD_FIXED_FEE',
                'value' => '0',
                'description' => '$10 Fee',
                'created_at' => NULL,
                'updated_at' => '2018-11-30 06:18:38',
            ),
            1 => 
            array (
                'id' => 100,
                'key' => 'CURRENCY_SIGN',
                'value' => '$',
                'description' => NULL,
                'created_at' => '2018-05-29 21:13:55',
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 101,
                'key' => 'DEPOSIT_WECHAT_MAX_AMOUNT',
                'value' => '300000.00',
                'description' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}