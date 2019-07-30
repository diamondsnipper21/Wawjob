<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserSkillsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_skills', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index();
			$table->integer('skill_id')->unsigned()->index();
			$table->boolean('priority')->default(0);
			$table->boolean('level')->default(0);
			$table->boolean('is_verified')->default(0);
			$table->boolean('is_active')->default(1);
			$table->index(['user_id','skill_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_skills');
	}

}
