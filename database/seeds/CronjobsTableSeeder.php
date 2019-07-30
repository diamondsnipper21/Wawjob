<?php

use Illuminate\Database\Seeder;

class CronjobsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cronjobs')->delete();
        
        \DB::table('cronjobs')->insert(array (
            0 => 
            array (
                'id' => 10,
                'type' => 7,
                'max_runtime' => 2,
                'status' => 4,
                'created_at' => '2017-10-26 17:09:30',
                'updated_at' => '2018-08-21 15:09:35',
                'done_at' => '2018-08-21 15:09:35',
            ),
            1 => 
            array (
                'id' => 11,
                'type' => 5,
                'max_runtime' => 0,
                'status' => 4,
                'created_at' => '2017-10-26 17:12:42',
                'updated_at' => '2018-05-01 11:40:17',
                'done_at' => '2018-05-01 03:40:17',
            ),
            2 => 
            array (
                'id' => 12,
                'type' => 8,
                'max_runtime' => 0,
                'status' => 4,
                'created_at' => '2017-10-26 17:15:11',
                'updated_at' => '2018-05-01 11:43:41',
                'done_at' => '2018-05-01 03:43:41',
            ),
        ));
        \DB::table('cronjobs')->insert(array (
            0 => 
            array (
                'id' => 13,
                'type' => 9,
                'max_runtime' => 1,
                'status' => 4,
                'created_at' => '2017-10-26 17:15:33',
                'updated_at' => '2018-08-21 09:09:33',
                'done_at' => '2018-08-21 09:09:33',
            ),
            1 => 
            array (
                'id' => 14,
                'type' => 4,
                'max_runtime' => 0,
                'status' => 4,
                'created_at' => '2017-10-26 17:17:24',
                'updated_at' => '2018-08-13 08:10:02',
                'done_at' => '2018-08-13 00:10:02',
            ),
            2 => 
            array (
                'id' => 15,
                'type' => 2,
                'max_runtime' => 1,
                'status' => 4,
                'created_at' => '2017-10-26 17:23:59',
                'updated_at' => '2018-08-11 08:00:06',
                'done_at' => '2018-08-11 00:00:06',
            ),
        ));
        \DB::table('cronjobs')->insert(array (
            0 => 
            array (
                'id' => 16,
                'type' => 3,
                'max_runtime' => 0,
                'status' => 4,
                'created_at' => '2017-10-26 17:24:13',
                'updated_at' => '2018-08-27 04:37:49',
                'done_at' => '2018-08-27 04:37:49',
            ),
            1 => 
            array (
                'id' => 17,
                'type' => 1,
                'max_runtime' => 0,
                'status' => 4,
                'created_at' => '2017-10-26 17:24:35',
                'updated_at' => '2018-08-16 21:00:01',
                'done_at' => '2018-08-16 13:00:01',
            ),
            2 => 
            array (
                'id' => 18,
                'type' => 11,
                'max_runtime' => 1,
                'status' => 4,
                'created_at' => '2017-12-19 02:20:40',
                'updated_at' => '2018-08-25 13:23:16',
                'done_at' => '2018-08-25 13:23:16',
            ),
        ));
        \DB::table('cronjobs')->insert(array (
            0 => 
            array (
                'id' => 19,
                'type' => 12,
                'max_runtime' => 0,
                'status' => 4,
                'created_at' => '2018-01-10 01:12:36',
                'updated_at' => '2018-08-16 20:30:01',
                'done_at' => '2018-08-16 12:30:01',
            ),
            1 => 
            array (
                'id' => 20,
                'type' => 13,
                'max_runtime' => 0,
                'status' => 4,
                'created_at' => '2018-01-17 02:47:41',
                'updated_at' => '2018-08-16 08:00:02',
                'done_at' => '2018-08-16 00:00:02',
            ),
            2 => 
            array (
                'id' => 21,
                'type' => 14,
                'max_runtime' => 0,
                'status' => 4,
                'created_at' => '2018-01-19 02:18:24',
                'updated_at' => '2018-08-25 13:09:01',
                'done_at' => '2018-08-25 13:09:01',
            ),
        ));
        \DB::table('cronjobs')->insert(array (
            0 => 
            array (
                'id' => 22,
                'type' => 10,
                'max_runtime' => 1,
                'status' => 4,
                'created_at' => '2018-01-25 04:37:56',
                'updated_at' => '2018-08-27 04:38:54',
                'done_at' => '2018-08-27 04:38:54',
            ),
            1 => 
            array (
                'id' => 23,
                'type' => 15,
                'max_runtime' => 0,
                'status' => 4,
                'created_at' => '2018-02-02 06:06:13',
                'updated_at' => '2018-08-25 14:23:22',
                'done_at' => '2018-08-25 14:23:22',
            ),
            2 => 
            array (
                'id' => 24,
                'type' => 6,
                'max_runtime' => 0,
                'status' => 4,
                'created_at' => '2018-02-02 06:06:13',
                'updated_at' => '2018-08-21 09:04:04',
                'done_at' => '2018-08-21 09:04:04',
            ),
        ));
        \DB::table('cronjobs')->insert(array (
            0 => 
            array (
                'id' => 25,
                'type' => 16,
                'max_runtime' => 0,
                'status' => 4,
                'created_at' => '2018-05-08 06:36:47',
                'updated_at' => '2018-08-14 23:10:20',
                'done_at' => '2018-08-14 15:10:20',
            ),
            1 => 
            array (
                'id' => 26,
                'type' => 17,
                'max_runtime' => 0,
                'status' => 4,
                'created_at' => '2018-05-08 06:36:47',
                'updated_at' => '2018-08-16 20:30:01',
                'done_at' => '2018-08-16 12:30:01',
            ),
            2 => 
            array (
                'id' => 27,
                'type' => 18,
                'max_runtime' => 1,
                'status' => 4,
                'created_at' => '2018-05-08 06:36:47',
                'updated_at' => '2018-08-28 03:49:27',
                'done_at' => '2018-08-28 03:49:27',
            ),
        ));
        \DB::table('cronjobs')->insert(array (
            0 => 
            array (
                'id' => 28,
                'type' => 20,
                'max_runtime' => 0,
                'status' => 4,
                'created_at' => '2018-05-08 06:36:47',
                'updated_at' => '2018-08-16 20:40:02',
                'done_at' => '2018-08-16 12:40:02',
            ),
            1 => 
            array (
                'id' => 29,
                'type' => 19,
                'max_runtime' => 4,
                'status' => 1,
                'created_at' => '2018-08-15 16:00:02',
                'updated_at' => '2018-08-27 13:27:33',
                'done_at' => '2018-08-22 10:07:05',
            ),
            2 => 
            array (
                'id' => 30,
                'type' => 21,
                'max_runtime' => 0,
                'status' => 0,
                'created_at' => '2018-08-15 16:00:02',
                'updated_at' => '2018-08-27 13:27:33',
                'done_at' => '2018-08-22 10:07:05',
            ),
        ));
        
        
    }
}