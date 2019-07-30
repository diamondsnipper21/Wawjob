<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserSavedProjectsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_saved_projects', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('project_id')->unsigned()->default(0)->index('project_id');
			$table->integer('user_id')->unsigned()->default(0)->index('user_id');
			$table->dateTime('posted_at')->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_saved_projects');
	}

}
