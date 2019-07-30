<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserCompaniesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_companies', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->string('name', 512);
			$table->string('website', 512)->nullable();
			$table->string('tagline', 512)->nullable();
			$table->string('description', 4096)->nullable();
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
		Schema::dropIfExists('user_companies');
	}

}
