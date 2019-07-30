<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProfileViewHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('profile_view_history', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('buyer_id')->unsigned()->default(0);
			$table->integer('user_id')->unsigned()->default(0);
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
		Schema::dropIfExists('profile_view_history');
	}

}
