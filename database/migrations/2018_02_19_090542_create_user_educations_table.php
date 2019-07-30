<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserEducationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_educations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index();
			$table->string('school', 512);
			$table->string('degree', 128);
			$table->string('major', 128);
			$table->string('desc', 1024);
			$table->date('from')->nullable();
			$table->date('to')->nullable();
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
		Schema::dropIfExists('user_educations');
	}

}
