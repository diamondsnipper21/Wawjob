<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserNotificationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_notifications', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('notification_id')->unsigned()->index();
			$table->string('notification', 1024);
			$table->integer('sender_id')->unsigned()->index();
			$table->integer('receiver_id')->unsigned()->index();
			$table->integer('data')->unsigned()->nullable()->comment('ticket notification인 경우 ticket_id');
			$table->dateTime('notified_at')->nullable();
			$table->dateTime('read_at')->nullable();
			$table->dateTime('admin_read_at')->nullable();
			$table->dateTime('valid_date')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_notifications');
	}

}
