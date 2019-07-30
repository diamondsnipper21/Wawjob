<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProjectSkillsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('project_skills', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('project_id')->unsigned()->index();
			$table->integer('skill_id')->unsigned()->index();
			$table->boolean('order')->default(0);
			$table->boolean('level')->default(0);
			$table->index(['project_id','skill_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('project_skills');
	}

}
