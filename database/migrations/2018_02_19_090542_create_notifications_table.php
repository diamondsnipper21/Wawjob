<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNotificationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('notifications', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('slug', 128);
			$table->text('content', 65535);
			$table->boolean('status')->default(1)->comment('0: Disabled, 1: Enabled');
			$table->boolean('is_const')->default(0);
			$table->boolean('type')->default(0);
			$table->boolean('priority')->nullable()->default(3)->comment('1: urgent 2: high 3: low');
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
		Schema::dropIfExists('notifications');
	}

}
