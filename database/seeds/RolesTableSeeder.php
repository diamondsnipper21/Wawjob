<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('roles')->delete();
        DB::table('roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'User',
                'slug' => 'user',
                'desc' => 'This is an account',
                'parent_id' => NULL,
                'is_active' => 1,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Super Administrator',
                'slug' => 'user_sadmin',
                'desc' => 'This is an account for super administrator',
                'parent_id' => 1,
                'is_active' => 1,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Administrator',
                'slug' => 'user_admin',
                'desc' => 'This is an account for administrator',
                'parent_id' => 1,
                'is_active' => 1,
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Buyer',
                'slug' => 'user_buyer',
                'desc' => 'This is an account for buyer',
                'parent_id' => 1,
                'is_active' => 1,
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Freelancer',
                'slug' => 'user_freelancer',
                'desc' => 'This is an account for freelancer',
                'parent_id' => 1,
                'is_active' => 1,
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}