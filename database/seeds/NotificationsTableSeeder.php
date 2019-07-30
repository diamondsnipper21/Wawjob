<?php

use Illuminate\Database\Seeder;

class NotificationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('notifications')->delete();
        
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 1,
                'slug' => 'ACCOUNT_SUSPENDED',
                'content' => '{"en":"Your account has been suspended.","ch":"Your account has been suspended."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 1,
                'priority' => 3,
                'created_at' => '2018-05-29 21:03:29',
                'updated_at' => '2018-05-29 13:03:28',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'slug' => 'ACCOUNT_REACTIVATED',
                'content' => '{"en":"Your account has been activated","ch":"Your account has been activated"}',
                'status' => 1,
                'is_const' => 1,
                'type' => 1,
                'priority' => 3,
                'created_at' => '2018-05-29 21:03:25',
                'updated_at' => '2018-05-29 13:03:23',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'slug' => 'FINANCIAL_ACCOUNT_SUSPENDED',
                'content' => '{"en":"Your financial account has been suspended.","ch":"Your financial account has been suspended."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 1,
                'priority' => 3,
                'created_at' => '2018-05-29 22:12:56',
                'updated_at' => '2018-05-29 14:12:54',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 4,
                'slug' => 'FINANCIAL_ACCOUNT_REACTIVATED',
                'content' => '{"en":"Your financial account has been activated.","ch":"Your financial account has been activated."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 1,
                'priority' => 3,
                'created_at' => '2018-05-29 22:12:48',
                'updated_at' => '2018-05-29 14:12:46',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 5,
                'slug' => 'RECEIVED_JOB_OFFER',
                'content' => '{"en":"You\'ve received an offer for the job \\"@#project#\\".","ch":"You\'ve received an offer for the job \\"@#project#\\"."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:52:15',
                'updated_at' => '2018-05-29 14:52:13',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 6,
                'slug' => 'RECEIVED_INVITATION',
                'content' => '{"en":"You\'ve received an invitation for the job \\"@#job_title#\\".","ch":"You\'ve received an invitation for the job \\"@#job_title#\\"."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:39:40',
                'updated_at' => '2018-05-29 14:39:39',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 7,
                'slug' => 'BUYER_JOB_CANCELLED',
                'content' => '{"en":"The job \\"@#job_title#\\" has been cancelled.","ch":"The job \\"@#job_title#\\" has been cancelled."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:17:13',
                'updated_at' => '2018-05-29 14:17:11',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 8,
                'slug' => 'APPLICATION_DECLINED',
                'content' => '{"en":"Your application was declined.","ch":"Your application was declined."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 21:33:44',
                'updated_at' => '2018-05-29 13:33:42',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 9,
                'slug' => 'PAY_BONUS',
                'content' => '{"en":"You received @#amount# bonus from @#buyer_name#.","ch":"You received @#amount# bonus from @#buyer_name#."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:34:40',
                'updated_at' => '2018-05-29 14:34:39',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 10,
                'slug' => 'PAY_FIXED',
                'content' => '{"en":"You\'ve received @#amount# from @#buyer_name#.","ch":"You\'ve received @#amount# from @#buyer_name#."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:38:28',
                'updated_at' => '2018-05-29 14:38:27',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 11,
                'slug' => 'REFUND',
                'content' => '{"en":"You\'ve refunded @#amount# to @#buyer_name#.","ch":"You\'ve refunded @#amount# to @#buyer_name#."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:41:43',
                'updated_at' => '2018-05-29 14:41:41',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 12,
                'slug' => 'TIMELOG_REVIEW',
                'content' => '{"en":"The work week has ended. Please review your weekly timelogs.","ch":"The work week has ended. Please review your weekly timelogs."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:58:55',
                'updated_at' => '2018-05-29 14:58:54',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 13,
                'slug' => 'BUYER_DEPOSIT',
                'content' => '{"en":"You\'ve deposited @#amount# to your account.","ch":"You\'ve deposited @#amount# to your account."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 23:05:45',
                'updated_at' => '2018-05-29 15:05:44',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 14,
                'slug' => 'USER_WITHDRAWAL',
                'content' => '{"en":"You\'ve withdrawn @#amount# from your account.","ch":"You\'ve withdrawn @#amount# from your account."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 23:01:22',
                'updated_at' => '2018-05-29 15:01:20',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 15,
                'slug' => 'TICKET_CREATED',
                'content' => '{"en":"The ticket #@#TICKET_ID# - \\"@#TICKET_NAME#\\" created.","ch":"The ticket #@#TICKET_ID# - \\"@#TICKET_NAME#\\" created."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-06-09 12:49:25',
                'updated_at' => '2018-06-09 04:49:28',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 16,
                'slug' => 'TICKET_CLOSED',
                'content' => '{"en":"The ticket #@#TICKET_ID# - \\"@#TICKET_NAME#\\" closed.","ch":"The ticket #@#TICKET_ID# - \\"@#TICKET_NAME#\\" closed."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-06-09 12:48:28',
                'updated_at' => '2018-06-09 04:48:30',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 17,
                'slug' => 'TICKET_SOLVED',
                'content' => '{"en":"The ticket #@#TICKET_ID# - \\"@#TICKET_NAME#\\" solved and closed.","ch":"The ticket #@#TICKET_ID# - \\"@#TICKET_NAME#\\" solved and closed."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-06-09 12:48:40',
                'updated_at' => '2018-06-09 04:48:42',
                'deleted_at' => '2018-06-09 04:48:42',
            ),
            2 => 
            array (
                'id' => 20,
                'slug' => 'CONTRACT_STARTED',
                'content' => '{"en":"The contract \\"@#contract_title#\\" was started.","ch":"The contract \\"@#contract_title#\\" was started."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:07:59',
                'updated_at' => '2018-05-29 14:07:57',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 21,
                'slug' => 'CONTRACT_CLOSED',
                'content' => '{"en":"The contract \\"@#contract_title#\\" was closed. Please leave your feedback.","ch":"The contract \\"@#contract_title#\\" was closed. Please leave your feedback."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:02:38',
                'updated_at' => '2018-05-29 14:02:37',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 22,
                'slug' => 'FREELANCER_ENABLED_CHANGE_FEEDBACK',
                'content' => '{"EN":"Your contractor has enabled you change the feedback for the contract @#contract_title#."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:21:58',
                'updated_at' => '2018-05-29 14:21:57',
                'deleted_at' => '2018-05-29 14:21:57',
            ),
            2 => 
            array (
                'id' => 23,
                'slug' => 'BUYER_CHANGED_FEEDBACK',
                'content' => '{"EN":"Your client has changed the feedback for the contract  @#contract_title#."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 21:36:11',
                'updated_at' => '2018-05-29 13:36:10',
                'deleted_at' => '2018-05-29 13:36:10',
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 24,
                'slug' => 'SEND_MESSAGE',
                'content' => '{"en":"@#sender_name# sent you a message.","ch":"@#sender_name# sent you a message."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:44:55',
                'updated_at' => '2018-05-29 14:44:54',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 25,
                'slug' => 'BUYER_PAY_BONUS',
                'content' => '{"en":"You\'ve given @#freelancer_name# @#amount# bonus.","ch":"You\'ve given @#freelancer_name# @#amount# bonus."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 23:02:48',
                'updated_at' => '2018-05-29 15:02:46',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 26,
                'slug' => 'BUYER_PAY_FIXED',
                'content' => '{"en":"You paid @#freelancer_name# @#amount#.","ch":"You paid @#freelancer_name# @#amount#."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 23:03:00',
                'updated_at' => '2018-05-29 15:02:59',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 27,
                'slug' => 'BUYER_REFUND',
                'content' => '{"en":"@#freelancer_name# refunded @#amount#.","ch":"@#freelancer_name# refunded @#amount#."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 21:59:24',
                'updated_at' => '2018-05-29 13:59:22',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 28,
                'slug' => 'AFFILIATE_USER',
                'content' => '{"EN":"Hello, @#affiliate_user#.\\nI would like to sign up <a href=\\"\\"@#signup#\\"\\">Wawjob<\\/a> and enjoy your business.\\nThanks."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 21:30:22',
                'updated_at' => '2018-05-29 13:30:20',
                'deleted_at' => '2018-05-29 13:30:20',
            ),
            2 => 
            array (
                'id' => 29,
                'slug' => 'BUYER_PAY_HOURLY',
                'content' => '{"en":"You paid @#freelancer_name# @#amount#.","ch":"You paid @#freelancer_name# @#amount#."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 23:03:12',
                'updated_at' => '2018-05-29 15:03:11',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 30,
                'slug' => 'PAY_HOURLY',
                'content' => '{"en":"You\'ve received @#amount# from @#buyer_name#.","ch":"You\'ve received @#amount# from @#buyer_name#."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:38:48',
                'updated_at' => '2018-05-29 14:38:47',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 31,
                'slug' => 'FREELANCER_REQUESTED_MILESTONE_PAYMENT',
                'content' => '{"en":"Payment request for the milestone \\"@#milestone_name#\\" of \\"@#contract_title#\\"","ch":"Payment request for the milestone \\"@#milestone_name#\\" of \\"@#contract_title#\\""}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:23:48',
                'updated_at' => '2018-05-29 14:23:46',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 32,
                'slug' => 'FREELANCER_ACCEPTED_MILESTONES_CHANGED',
                'content' => '{"EN":"@#sender_name# accepted milestones changed for @#contract_title#."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:20:07',
                'updated_at' => '2018-05-29 14:20:06',
                'deleted_at' => '2018-05-29 14:20:06',
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 33,
                'slug' => 'FREELANCER_DECLINED_MILESTONES_CHANGED',
                'content' => '{"EN":"@#sender_name# declined milestones changed for @#contract_title#."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:20:07',
                'updated_at' => '2018-05-29 14:20:06',
                'deleted_at' => '2018-05-29 14:20:06',
            ),
            1 => 
            array (
                'id' => 34,
                'slug' => 'BUYER_FUND',
                'content' => '{"en":"You deposited @#amount# in Escrow for the milestone \\"@#milestone_title#\\".","ch":"You deposited @#amount# in Escrow for the milestone \\"@#milestone_title#\\"."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 23:09:19',
                'updated_at' => '2018-05-29 15:09:18',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 35,
                'slug' => 'FUND',
                'content' => '{"en":"@#buyer_name# deposited @#amount# in Escrow for the milestone \\"@#milestone_title#\\".","ch":"@#buyer_name# deposited @#amount# in Escrow for the milestone \\"@#milestone_title#\\"."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 23:09:31',
                'updated_at' => '2018-05-29 15:09:30',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 36,
                'slug' => 'RELEASE',
                'content' => '{"en":"@#buyer_name# released @#amount# for the milestone \\"@#milestone_title#\\".","ch":"@#buyer_name# released @#amount# for the milestone \\"@#milestone_title#\\"."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:43:50',
                'updated_at' => '2018-05-29 14:43:48',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 37,
                'slug' => 'BUYER_RELEASE',
                'content' => '{"en":"You released @#amount# for the milestone \\"@#milestone_title#\\".","ch":"You released @#amount# for the milestone \\"@#milestone_title#\\"."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:44:19',
                'updated_at' => '2018-05-29 14:44:17',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 38,
                'slug' => 'CONTRACT_WEEK_LIMIT_HRS',
                'content' => '{"en":"Terms changed for the contract \\"@#contract#\\". New weekly limit: @#limit# hours \\/ week.","ch":"Terms changed for the contract \\"@#contract#\\". New weekly limit: @#limit# hours \\/ week."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:10:17',
                'updated_at' => '2018-05-29 14:10:16',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 39,
                'slug' => 'CONTRACT_WEEK_LIMIT_NO',
                'content' => '{"en":"Terms changed for the contract \\"@#contract#\\". No weekly limit.","ch":"Terms changed for the contract \\"@#contract#\\". No weekly limit."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:11:35',
                'updated_at' => '2018-05-29 14:11:33',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 40,
                'slug' => 'CONTRACT_ALLOWED_MANUAL_TIME',
                'content' => '{"en":"Terms changed for the contract \\"@#contract#\\". You can add manual time.","ch":"Terms changed for the contract \\"@#contract#\\". You can add manual time."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:06:43',
                'updated_at' => '2018-05-29 14:06:42',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 41,
                'slug' => 'CONTRACT_NOT_ALLOWED_MANUAL_TIME',
                'content' => '{"en":"Terms changed for the contract \\"@#contract#\\". You cannot add manual time.","ch":"Terms changed for the contract \\"@#contract#\\". You cannot add manual time."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:06:07',
                'updated_at' => '2018-05-29 14:06:05',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 42,
                'slug' => 'SENT_OFFER',
                'content' => '{"EN":"@#buyer# has sent you offers for his job - @#project#."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:52:32',
                'updated_at' => '2018-05-29 14:52:31',
                'deleted_at' => '2018-05-29 14:52:31',
            ),
            1 => 
            array (
                'id' => 43,
                'slug' => 'TICKET_ASSIGNED',
                'content' => '{"en":"The ticket #@#ticket_id# assigned to you.","ch":"The ticket #@#ticket_id# assigned to you."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 1,
                'priority' => 3,
                'created_at' => '2018-06-09 12:49:54',
                'updated_at' => '2018-06-09 04:49:56',
                'deleted_at' => '2018-06-09 04:49:56',
            ),
            2 => 
            array (
                'id' => 44,
                'slug' => 'ADMIN_TICKET_ASSIGNED',
                'content' => '{"en":"The ticket #@#TICKET_ID# - \\"@#TICKET_NAME#\\" has been assigned to you by @#admin_name#.","ch":"The ticket #@#TICKET_ID# - \\"@#TICKET_NAME#\\" has been assigned to you by @#admin_name#."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 1,
                'priority' => 3,
                'created_at' => '2018-06-09 12:52:46',
                'updated_at' => '2018-06-09 04:52:48',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 45,
                'slug' => 'ADMIN_TICKET_CLOSED',
                'content' => '{"en":"The ticket #@#ticket_id# has been closed by @#admin_name#.","ch":"The ticket #@#ticket_id# has been closed by @#admin_name#."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 1,
                'priority' => 3,
                'created_at' => '2018-06-09 12:53:46',
                'updated_at' => '2018-06-09 04:53:49',
                'deleted_at' => '2018-06-09 04:53:49',
            ),
            1 => 
            array (
                'id' => 46,
                'slug' => 'ADMIN_TICKET_SOLVED',
                'content' => '{"en":"The ticket #@#ticket_id# has been solved and closed.","ch":"The ticket #@#ticket_id# has been solved and closed."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 1,
                'priority' => 3,
                'created_at' => '2018-06-09 12:49:54',
                'updated_at' => '2018-06-09 04:49:56',
                'deleted_at' => '2018-06-09 04:49:56',
            ),
            2 => 
            array (
                'id' => 47,
                'slug' => 'ADMIN_TICKET_CREATED',
                'content' => '{"EN":"The ticket #@#ticket_id# has been created."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 1,
                'priority' => 3,
                'created_at' => '2018-05-29 21:04:52',
                'updated_at' => '2018-05-29 13:04:51',
                'deleted_at' => '2018-05-29 13:04:51',
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 48,
                'slug' => 'JOB_ACTIVATED',
                'content' => '{"en":"The job \\"@#project#\\"  has been activated.","ch":"The job \\"@#project#\\"  has been activated."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:13:55',
                'updated_at' => '2018-05-29 14:13:54',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 49,
                'slug' => 'JOB_SUSPENDED',
                'content' => '{"en":"The job \\"@#project#\\"  has been suspended.","ch":"The job \\"@#project#\\"  has been suspended."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:14:23',
                'updated_at' => '2018-05-29 14:14:22',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 50,
                'slug' => 'JOB_DELETED',
                'content' => '{"en":"The job \\"@#project#\\"  has been deleted.","ch":"The job \\"@#project#\\"  has been deleted."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:14:11',
                'updated_at' => '2018-05-29 14:14:10',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 51,
                'slug' => 'FREELANCER_ACCEPTED_OFFER',
                'content' => '{"en":"@#sender_name# accepted your offer for \\"@#offer_title#\\".","ch":"@#sender_name# accepted your offer for \\"@#offer_title#\\"."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:20:53',
                'updated_at' => '2018-05-29 14:20:52',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 52,
                'slug' => 'FREELANCER_DECLINED_OFFER',
                'content' => '{"en":"@#sender_name# declined your offer \\"@#offer_title#\\".","ch":"@#sender_name# declined your offer \\"@#offer_title#\\"."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:21:09',
                'updated_at' => '2018-05-29 14:21:08',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 53,
                'slug' => 'BUYER_JOB_REPOSTED',
                'content' => '{"en":"The Job \\"@#job_title#\\"  has been reposted.","ch":"The Job \\"@#job_title#\\"  has been reposted."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:17:38',
                'updated_at' => '2018-05-29 14:17:37',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 54,
                'slug' => 'BUYER_JOB_CLOSED',
                'content' => '{"en":"The job \\"@#job_title#\\" has been closed.","ch":"The job \\"@#job_title#\\" has been closed."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:17:25',
                'updated_at' => '2018-05-29 14:17:23',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 55,
                'slug' => 'CONTRACT_SUSPENDED',
                'content' => '{"en":"The contract \\"@#contract_title#\\" was suspended.","ch":"The contract \\"@#contract_title#\\" was suspended."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:08:22',
                'updated_at' => '2018-05-29 14:08:21',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 56,
                'slug' => 'PAY_AFFILIATE',
                'content' => '{"en":"You\'ve received @#amount# for your referrals.","ch":"You\'ve received @#amount# for your referrals."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:37:48',
                'updated_at' => '2018-05-29 14:37:47',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 57,
                'slug' => 'CONTRACT_PAUSED',
                'content' => '{"en":"The contract \\"@#contract_title#\\" has been paused.","ch":"The contract \\"@#contract_title#\\" has been paused."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:07:12',
                'updated_at' => '2018-05-29 14:07:10',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 58,
                'slug' => 'CONTRACT_RESTARTED',
                'content' => '{"en":"The contract \\"@#contract_title#\\" has been restarted.","ch":"The contract \\"@#contract_title#\\" has been restarted."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:07:26',
                'updated_at' => '2018-05-29 14:07:24',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 59,
                'slug' => 'REFUNDED_FUND',
                'content' => '{"en":"@#amount# for the milestone \\"@#milestone_title#\\" has been refunded.","ch":"@#amount# for the milestone \\"@#milestone_title#\\" has been refunded."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:42:32',
                'updated_at' => '2018-05-29 14:42:31',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 60,
                'slug' => 'TICKET_CLOSED_WHEN_DELETING_ACCOUNT',
                'content' => '{"en":"The ticket #@#TICKET_ID# - \\"@#TICKET_NAME#\\" closed because of ban of \\"@#BANNED_USER#\\".","ch":"The ticket #@#TICKET_ID# - \\"@#TICKET_NAME#\\" closed because of ban of \\"@#BANNED_USER#\\"."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-06-09 12:51:32',
                'updated_at' => '2018-06-09 04:51:35',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 61,
                'slug' => 'CONTRACT_CLOSED_WHEN_DELETING_ACCOUNT',
                'content' => '{"en":"@#BANNED_USER# was banned permanently.The contract \\"@#TITLE#\\"  was ended accordingly.","ch":"@#BANNED_USER# was banned permanently.The contract \\"@#TITLE#\\"  was ended accordingly."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:04:00',
                'updated_at' => '2018-05-29 14:03:58',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 62,
                'slug' => 'TODO_ASSIGNED',
                'content' => '{"en":"TODO  assigned: \\"@#SUBJECT#\\" by @#CREATOR#","ch":"TODO  assigned: \\"@#SUBJECT#\\" by @#CREATOR#"}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 1,
                'created_at' => '2018-05-29 23:02:30',
                'updated_at' => '2018-05-29 15:02:28',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 63,
                'slug' => 'TODO_REOPEN',
                'content' => '{"en":"TODO reopened: \\"@#SUBJECT#\\"","ch":"TODO reopened: \\"@#SUBJECT#\\""}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 1,
                'created_at' => '2018-05-29 23:00:32',
                'updated_at' => '2018-05-29 15:00:30',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 64,
                'slug' => 'ADMIN_TICKET_CREATED',
                'content' => '{"en":"A ticket \\"@#SUBJECT#\\" has been created.","ch":"A ticket \\"@#SUBJECT#\\" has been created."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-06-09 12:53:46',
                'updated_at' => '2018-06-09 04:53:49',
                'deleted_at' => '2018-06-09 04:53:49',
            ),
            2 => 
            array (
                'id' => 65,
                'slug' => 'FEE_CHANGED',
                'content' => '{"en":"iJobDesk Service Fees have been changed!","ch":"iJobDesk Service Fees have been changed!"}',
                'status' => 1,
                'is_const' => 1,
                'type' => 1,
                'priority' => 3,
                'created_at' => '2018-05-29 22:12:34',
                'updated_at' => '2018-05-29 14:12:33',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 66,
                'slug' => 'OVERDUE_WITHDRAWS',
                'content' => '{"en":"Overdue withdrawals: @#TOTAL#","ch":"Overdue withdrawals: @#TOTAL#"}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 23:10:04',
                'updated_at' => '2018-05-29 15:10:03',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 67,
                'slug' => 'OVERDUE_AFFILIATE_TRANSACTIONS',
                'content' => '{"en":"Overdue affiliate payments: @#TOTAL#","ch":"Overdue affiliate payments: @#TOTAL#"}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 23:09:53',
                'updated_at' => '2018-05-29 15:09:51',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 68,
                'slug' => 'ERROR_CRON_JOB',
                'content' => '{"en":"Cronjob Error: @#NAME#","ch":"Cronjob Error: @#NAME#"}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:12:04',
                'updated_at' => '2018-05-29 14:12:03',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 69,
                'slug' => 'TODO_CLOSED',
                'content' => '{"en":"TODO  closed: \\"@#SUBJECT#\\"","ch":"TODO  closed: \\"@#SUBJECT#\\""}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 1,
                'created_at' => '2018-05-29 23:01:47',
                'updated_at' => '2018-05-29 15:01:45',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 70,
                'slug' => 'CONTRACT_CANCELLED',
                'content' => '{"en":"The contract \\"@#contract_title#\\" has been cancelled.","ch":"The contract \\"@#contract_title#\\" has been cancelled."}',
                'status' => 1,
                'is_const' => 1,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-05-29 22:01:54',
                'updated_at' => '2018-05-29 14:01:53',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 71,
                'slug' => 'DISPUTE_LASTWEEK_BUYER',
            'content' => '{"en":"You\'ve disputed and got @#AMOUNT# refund (@#HOURS# hours of the contract \\"@#CONTRACT_NAME#\\") for the last week (@#DATE_FROM# - @#DATE_TO#)","ch":"You\'ve disputed and got @#AMOUNT# refund (@#HOURS# hours of the contract \\"@#CONTRACT_NAME#\\") for the last week (@#DATE_FROM# - @#DATE_TO#)"}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-06-05 12:28:21',
                'updated_at' => '2018-06-05 04:28:22',
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('notifications')->insert(array (
            0 => 
            array (
                'id' => 72,
                'slug' => 'DISPUTE_LASTWEEK_FREELANCER',
            'content' => '{"en":"@#BUYER_NAME# disputed for the last week\'s timelogs of the contract \\"@#CONTRACT_NAME#\\". @#AMOUNT# (@#HOURS# hours not qualified) for @#DATE_FROM# - @#DATE_TO# refunded to the client.","ch":"@#BUYER_NAME# disputed for the last week\'s timelogs of the contract \\"@#CONTRACT_NAME#\\". @#AMOUNT# (@#HOURS# hours not qualified) for @#DATE_FROM# - @#DATE_TO# refunded to the client."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-06-05 12:34:51',
                'updated_at' => '2018-06-05 04:34:52',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 73,
                'slug' => 'LOGIN_BLOCKED_WRONG_PWD',
                'content' => '{"en":"@#USER_NAME# - login blocked because of wrong password.","ch":"@#USER_NAME# - login blocked because of wrong password."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-06-12 21:21:58',
                'updated_at' => '2018-06-12 13:21:57',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 74,
                'slug' => 'LOGIN_BLOCKED_WRONG_SQ',
                'content' => '{"en":"@#USER_NAME# - login blocked because of wrong security answer.","ch":"@#USER_NAME# - login blocked because of wrong security answer."}',
                'status' => 1,
                'is_const' => 0,
                'type' => 0,
                'priority' => 3,
                'created_at' => '2018-06-12 21:21:42',
                'updated_at' => '2018-06-12 13:21:41',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}