<?php

use Illuminate\Database\Seeder;

class SecurityQuestionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('security_questions')->delete();
        
        \DB::table('security_questions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'question' => '{"en":"What is your primary school name?","ch":"你的小学名字是什么？"}',
                'category_id' => 1,
                'is_active' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'question' => '{"en":"What is your best friend\'s nick name?","ch":"你最好的朋友的昵称是什么？"}',
                'category_id' => 1,
                'is_active' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'question' => '{"en":"What is your favorite football club?","ch":"你最喜欢的足球俱乐部是什么？"}',
                'category_id' => 1,
                'is_active' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));
        \DB::table('security_questions')->insert(array (
            0 => 
            array (
                'id' => 4,
                'question' => '{"en":"What is your favorite sport?","ch":"你最喜欢的运动是什么？"}',
                'category_id' => 1,
                'is_active' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 5,
                'question' => '{"en":"What is your mother\'s native home?","ch":"你母亲的家乡是什么？"}',
                'category_id' => 1,
                'is_active' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}