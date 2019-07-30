<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProjectInvitationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('project_invitations', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('sender_id')->default(0)->index('sender_id');
			$table->integer('receiver_id')->default(0)->index('receiver_id');
			$table->integer('project_id')->default(0)->index('project_id');
			$table->text('message', 65535)->nullable();
			$table->text('answer', 65535)->nullable()->comment('Accept or decline message from freelancer');
			$table->boolean('status')->default(0)->comment('0: Sent, 1: Accepted, 2: Declined, 3: Active');
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
		Schema::dropIfExists('project_invitations');
	}

}
