<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('users')->delete();
        DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'username' => 'admin',
                'email' => 'super@ijobdesk.com',
                'password' => '$2y$10$lD9pOt3e8Eos.rqW5I/F/O7wKa9v9b9U15DzD5oyFSkaTyA/vyOKG',
                'status' => 1,
                'try_login' => 0,
                'try_password' => 0,
                'try_question' => 0,
                'remember_token' => 'qzjfPHokIu3ffPsjaQZX09VwKrTUiqwZp5tWOCyD9vf1gVmuotPUv5PoKqO5',
                'locale' => NULL,
                'role' => 4,
                'is_auto_suspended' => NULL,
                'closed_reason' => 0,
                'created_at' => '2016-03-16 13:47:10',
                'updated_at' => '2018-02-15 05:07:34',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}