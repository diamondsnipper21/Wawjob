<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTicketsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tickets', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('subject');
			$table->text('content', 65535)->nullable();
			$table->integer('user_id')->unsigned()->index();
			$table->string('email')->nullable()->comment('Added from contact us');
			$table->integer('admin_id')->unsigned()->nullable()->index();
			$table->integer('assigner_id')->nullable();
			$table->string('reader_ids', 50)->nullable();
			$table->integer('closer_id')->nullable()->comment('User who close this ticket');
			$table->integer('contract_id')->unsigned()->default(0)->index();
			$table->boolean('type')->default(0);
			$table->boolean('priority')->default(0);
			$table->boolean('status')->default(1);
			$table->text('memo', 65535)->nullable();
			$table->integer('archive_type')->nullable();
			$table->text('reason', 65535)->nullable();
			$table->integer('dispute_winner_id')->nullable();
			$table->dateTime('assigned_at')->nullable();
			$table->dateTime('ended_at')->nullable();
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
		Schema::dropIfExists('tickets');
	}

}
