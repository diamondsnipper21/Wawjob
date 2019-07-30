<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProjectMessageThreadsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('project_message_threads', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('subject');
			$table->integer('sender_id')->unsigned()->default(0)->index();
			$table->integer('receiver_id')->unsigned()->default(0)->index();
			$table->integer('application_id')->unsigned()->default(0)->index();
			$table->boolean('is_favourite')->default(0)->comment('1:favourite');
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
		Schema::dropIfExists('project_message_threads');
	}

}
