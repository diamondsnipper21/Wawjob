<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProjectOffersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('project_offers', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('sender_id')->default(0);
			$table->integer('receiver_id')->default(0)->index('receiver_id');
			$table->integer('project_id')->default(0)->index('project_id');
			$table->integer('contract_id')->default(0)->index('contract_id');
			$table->date('start_date')->default('0000-00-00');
			$table->boolean('status')->default(0)->comment('0: Sent, 1: Accepted, 2: Freelancer declined, 3: Buyer withdrawn');
			$table->timestamps();
			$table->softDeletes();
			$table->index(['sender_id','receiver_id','project_id'], 'sender_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('project_offers');
	}

}
