<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserEmploymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_employments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index();
			$table->string('company', 512);
			$table->string('position', 128);
			$table->string('desc', 1024);
			$table->smallInteger('from_year')->nullable();
			$table->boolean('from_month')->nullable();
			$table->smallInteger('to_year')->nullable();
			$table->boolean('to_month')->nullable();
			$table->boolean('is_verified')->default(0);
			$table->boolean('is_active')->default(0);
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_employments');
	}

}
