<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCronjobsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cronjobs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean('type')->default(0)->index();
			$table->integer('max_runtime')->unsigned()->default(0)->index();
			$table->boolean('status')->default(0)->index();
			$table->timestamps();
			$table->dateTime('done_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('cronjobs');
	}

}
