<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActionHistoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('action_histories', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('type')->nullable()->comment('1: User 2: Job');
			$table->string('action_type', 20)->nullable();
			$table->integer('doer_id')->nullable()->index('doer_id')->comment('admin id');
			$table->integer('target_id')->nullable()->index('target_id')->comment('contract, user, job');
			$table->text('description', 65535)->nullable()->comment('description of action');
			$table->text('reason', 65535)->nullable();
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
		Schema::dropIfExists('action_histories');
	}

}
