<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTicketCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ticket_comments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('ticket_id')->unsigned()->index();
			$table->integer('sender_id')->unsigned()->index('ticket_comments_commentor_id_index');
			$table->text('message', 65535);
			$table->string('reader_ids', 50)->nullable();
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
		Schema::dropIfExists('ticket_comments');
	}

}
