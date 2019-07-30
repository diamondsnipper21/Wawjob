<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTodosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('todos', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('subject')->default('0');
			$table->boolean('type')->default(0);
			$table->integer('creator_id')->default(0);
			$table->string('assigner_ids', 50)->default('0');
			$table->boolean('priority')->default(0);
			$table->date('due_date')->nullable();
			$table->integer('related_ticket_id')->default(0);
			$table->text('description', 65535)->nullable();
			$table->boolean('status')->nullable()->default(1)->comment('1: opening 2: complete 3: cancel');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('todos');
	}

}
