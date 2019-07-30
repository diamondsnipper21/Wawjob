<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserAffiliatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_affiliates', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->default(0);
			$table->integer('affiliate_id')->unsigned()->default(0);
			$table->string('email');
			$table->boolean('status')->default(0);
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
		Schema::dropIfExists('user_affiliates');
	}

}
