<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReasonsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reasons', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->text('message', 65535)->nullable();
			$table->boolean('reason')->default(0)->comment('1: Unresponsive, 2: Disupte, 3: Other');
			$table->integer('admin_id')->nullable()->default(0)->index('admin_id');
			$table->boolean('type')->nullable()->default(0)->comment('1: user 2: project 3: contract 4: proposal 5: message_thread');
			$table->integer('affected_id')->nullable()->default(0)->index('affected_id');
			$table->boolean('action')->nullable()->default(0)->comment('-1: delete');
			$table->dateTime('created_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('reasons');
	}

}
