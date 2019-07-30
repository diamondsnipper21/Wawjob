<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserIgnoredWarningsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_ignored_warnings', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->nullable()->index('user_id');
			$table->integer('type')->nullable()->index('type')->comment('1: suspend 2: financial 3: leave feedback');
			$table->integer('target_id')->nullable()->comment('contract id or others...');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_ignored_warnings');
	}

}
