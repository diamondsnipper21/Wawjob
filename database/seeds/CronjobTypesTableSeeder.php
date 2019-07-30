<?php

use Illuminate\Database\Seeder;

class CronjobTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cronjob_types')->delete();
        
        \DB::table('cronjob_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'type' => 1,
                'name' => 'Update Timelogs & Timesheet based on WorkDiary
',
                'frequency' => 'Every an hour',
                'desc' => 'It updates timelogs and timesheet according to the WorkDiary. They will be updated immediately at the moment. It will correct the wrong mappings now.
',
                'created_at' => '2017-12-25 01:57:52',
                'updated_at' => '2017-12-25 01:57:52',
            ),
            1 => 
            array (
                'id' => 2,
                'type' => 2,
                'name' => 'Move Timelogs in Review to Pending State
',
            'frequency' => 'Every Saturday (00:00)',
                'desc' => 'It moves timelogs/payments in review state to pending state.
',
                'created_at' => '2017-12-25 01:57:57',
                'updated_at' => '2017-12-25 01:57:57',
            ),
            2 => 
            array (
                'id' => 3,
                'type' => 3,
                'name' => 'Move Timelogs in Pending to Available State
',
            'frequency' => 'Every day (01:00)',
                'desc' => 'It moves timelogs/payments that has been in pending state for more than 3 days to available state.
',
                'created_at' => '2017-12-25 01:58:02',
                'updated_at' => '2017-12-25 01:58:02',
            ),
        ));
        \DB::table('cronjob_types')->insert(array (
            0 => 
            array (
                'id' => 4,
                'type' => 4,
                'name' => 'Last Week Timelogs in Review State every Monday
',
            'frequency' => 'Every Monday (00:10)',
                'desc' => 'It closes last week workdiary and move the timelogs in Review State every Monday.',
                'created_at' => '2017-12-25 01:58:10',
                'updated_at' => '2017-12-25 01:58:10',
            ),
            1 => 
            array (
                'id' => 5,
                'type' => 5,
                'name' => 'Expire Job Postings
',
            'frequency' => 'Every day (04:00)',
                'desc' => 'It expires open job postings that have been posted for more than 30 days.',
                'created_at' => '2017-12-25 01:58:20',
                'updated_at' => '2017-12-25 01:58:20',
            ),
            2 => 
            array (
                'id' => 6,
                'type' => 6,
                'name' => 'Apply decreasing weekly limit changes every Monday
',
            'frequency' => 'Every Monday (00:20)',
                'desc' => 'It will apply new decreasing weekly limit changes to the contracts every Monday. Increasing weekly limit changes will be applied immediately. But decreasing will be applied next Monday.',
                'created_at' => '2017-12-25 01:58:25',
                'updated_at' => '2017-12-25 01:58:25',
            ),
        ));
        \DB::table('cronjob_types')->insert(array (
            0 => 
            array (
                'id' => 7,
                'type' => 7,
                'name' => 'Update User Stats',
            'frequency' => 'Every Sunday (00:00)',
                'desc' => 'It updates the freelancer weight according to the skills/search keywords.',
                'created_at' => '2017-12-25 01:58:34',
                'updated_at' => '2017-12-25 01:58:34',
            ),
            1 => 
            array (
                'id' => 8,
                'type' => 8,
                'name' => 'Update Freelancer Points By Skills',
            'frequency' => 'Every 1st day of Month (01:00)',
                'desc' => 'It updates the freelancer weight according to the skills/search keywords.',
                'created_at' => '2017-12-25 01:58:42',
                'updated_at' => '2017-12-25 01:58:42',
            ),
            2 => 
            array (
                'id' => 9,
                'type' => 9,
                'name' => 'Update Freelancer Connects',
            'frequency' => 'Every day (22:00)',
                'desc' => 'It updates freelancers\' connects, returning connects.',
                'created_at' => '2017-12-25 01:58:49',
                'updated_at' => '2017-12-25 01:58:49',
            ),
        ));
        \DB::table('cronjob_types')->insert(array (
            0 => 
            array (
                'id' => 10,
                'type' => 10,
                'name' => 'Make payments for Affiliates
',
            'frequency' => 'Every an hour (*: 20)',
                'desc' => 'It will make payments for affiliates when the payment is made in a contract. Affiliates will get paid immediately now, so it will double confirm if there are missing / pending payments for affiliates.',
                'created_at' => '2017-12-25 01:58:56',
                'updated_at' => '2017-12-25 01:58:56',
            ),
            1 => 
            array (
                'id' => 11,
                'type' => 11,
                'name' => 'Complete deposits in proceeding state
',
            'frequency' => 'Every an hour (*: 30)',
                'desc' => 'It will complete deposits that has been for more than an hour in proceeding state. In case you run this job manually, it will make all deposits in pending state completed immediately.',
                'created_at' => '2017-12-25 01:58:58',
                'updated_at' => '2017-12-25 01:58:58',
            ),
            2 => 
            array (
                'id' => 12,
                'type' => 12,
                'name' => 'Complete our site withdrawals in proceeding state
',
            'frequency' => 'Every an hour (*: 30)',
                'desc' => 'It will complete deposits that has been for more than an hour in proceeding state.',
                'created_at' => '2018-01-19 02:19:17',
                'updated_at' => '2018-01-19 02:19:17',
            ),
        ));
        \DB::table('cronjob_types')->insert(array (
            0 => 
            array (
                'id' => 13,
                'type' => 13,
                'name' => 'Send reminder emails to super admin for overdue withdrawals
',
            'frequency' => 'Every day (00:00)',
            'desc' => 'It will send reminder emails to super admin in case there are overdue withdrawal requests (more than 3 days).',
                'created_at' => '2018-06-11 07:28:18',
                'updated_at' => '2018-06-11 07:28:18',
            ),
            1 => 
            array (
                'id' => 14,
                'type' => 14,
                'name' => 'Activate user payment methods in pending state
',
            'frequency' => 'Every day (02:00)',
                'desc' => 'It will activate user payment methods in pending state. New payment methods will be pending for 3 days because of security reason.',
                'created_at' => '2018-01-19 02:19:23',
                'updated_at' => '2018-01-19 02:19:23',
            ),
            2 => 
            array (
                'id' => 15,
                'type' => 15,
                'name' => 'Check Affiliate Transactions',
            'frequency' => 'Every day (03:00)',
                'desc' => NULL,
                'created_at' => '2018-05-08 06:37:14',
                'updated_at' => '2018-05-08 06:37:14',
            ),
        ));
        \DB::table('cronjob_types')->insert(array (
            0 => 
            array (
                'id' => 16,
                'type' => 16,
                'name' => 'Disable expired credit cards
',
            'frequency' => 'Every 1st day of Month (00:10)',
                'desc' => 'It will disable expired credit cards.',
                'created_at' => '2018-05-08 06:37:19',
                'updated_at' => '2018-05-08 06:37:19',
            ),
            1 => 
            array (
                'id' => 17,
                'type' => 17,
                'name' => 'Complete withdrawals in proceeding state
',
            'frequency' => 'Every an hour (*: 30)',
                'desc' => 'It will complete withdrawals that has been for more than an hour in proceeding state.',
                'created_at' => '2018-06-11 00:59:57',
                'updated_at' => '2018-06-11 00:59:57',
            ),
            2 => 
            array (
                'id' => 18,
                'type' => 18,
                'name' => 'Run checksum Bot for Transaction History
',
            'frequency' => 'Every day (23:30)',
                'desc' => 'Bot will do checksum for transaction history.',
                'created_at' => '2018-06-12 19:30:37',
                'updated_at' => '2018-06-12 19:30:37',
            ),
        ));
        \DB::table('cronjob_types')->insert(array (
            0 => 
            array (
                'id' => 19,
                'type' => 19,
                'name' => 'Send emails for Job Recommendation
',
                'frequency' => 'Every 15 mins',
                'desc' => 'It will send email notifications to freelancers when a job is posted.',
                'created_at' => '2018-06-12 19:30:37',
                'updated_at' => '2018-06-12 19:30:37',
            ),
            1 => 
            array (
                'id' => 20,
                'type' => 20,
                'name' => 'Update buyer\'s job posting count',
            'frequency' => 'Every an hour (*: 40)',
                'desc' => 'It will update the buyer\'s job postings. It will be updated immediately.',
                'created_at' => '2018-06-12 19:30:37',
                'updated_at' => '2018-06-12 19:30:37',
            ),
            2 => 
            array (
                'id' => 21,
                'type' => 21,
                'name' => 'Update Freelancer Points',
            'frequency' => 'Every day (23:00)',
                'desc' => 'It will update freelancers\' weights, which will be very important for displaying freelancers in search page.',
                'created_at' => '2018-06-12 19:30:37',
                'updated_at' => '2018-06-12 19:30:37',
            ),
        ));
        
        
    }
}