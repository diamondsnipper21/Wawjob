<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserProfilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_profiles', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index();
			$table->string('title', 256)->nullable();
			$table->text('desc', 65535)->nullable();
			$table->float('rate', 10, 0)->unsigned()->default(0);
			$table->boolean('en_level')->default(0);
			$table->boolean('share')->default(0);
			$table->boolean('available')->default(0)->comment('0: > 30hrs, 1: 10~30 hrs, 2: Not Available');
			$table->timestamps();
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
		Schema::dropIfExists('user_profiles');
	}

}
