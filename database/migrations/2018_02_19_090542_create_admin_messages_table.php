<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdminMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('admin_messages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('message_type')->nullable();
			$table->integer('target_id')->unsigned()->nullable()->index('ticket_comments_ticket_id_index')->comment('Todo, Ticket, Contact Us');
			$table->integer('sender_id')->unsigned()->nullable()->index('ticket_comments_commentor_id_index');
			$table->text('message', 65535)->nullable();
			$table->string('reader_ids', 50)->nullable();
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
		Schema::dropIfExists('admin_messages');
	}

}
