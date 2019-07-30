<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProjectMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('project_messages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('thread_id')->unsigned()->nullable()->index();
			$table->integer('sender_id')->unsigned()->index();
			$table->string('message', 5001);
			$table->string('reader_ids', 100)->nullable();
			$table->dateTime('received_at')->nullable();
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
		Schema::dropIfExists('project_messages');
	}

}
