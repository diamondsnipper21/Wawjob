<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHourlyLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hourly_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('contract_id')->unsigned()->index();
			$table->text('comment', 65535)->nullable();
			$table->text('activity', 65535);
			$table->boolean('score')->default(0);
			$table->string('active_window');
			$table->boolean('is_manual')->nullable()->default(0);
			$table->boolean('is_overlimit')->nullable()->default(0);
			$table->boolean('is_calculated')->default(0);
			$table->boolean('is_deleted')->default(0);
			$table->dateTime('taken_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('hourly_logs');
	}

}
