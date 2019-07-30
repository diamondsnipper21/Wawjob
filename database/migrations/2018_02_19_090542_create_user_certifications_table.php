<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserCertificationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_certifications', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->nullable();
			$table->string('title', 256)->nullable();
			$table->string('url', 512)->nullable();
			$table->string('description', 1028)->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->boolean('month')->nullable();
			$table->integer('year')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_certifications');
	}

}
