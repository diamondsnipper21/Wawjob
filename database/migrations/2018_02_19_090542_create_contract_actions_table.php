<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContractActionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contract_actions', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('contract_id')->unsigned()->default(0);
			$table->integer('doer_id')->unsigned()->default(0);
			$table->boolean('status')->default(0);
			$table->text('comment', 65535)->nullable();
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
		Schema::dropIfExists('contract_actions');
	}

}
