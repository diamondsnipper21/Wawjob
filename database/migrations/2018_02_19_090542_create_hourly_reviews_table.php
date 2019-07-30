<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHourlyReviewsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hourly_reviews', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('contract_id')->unsigned()->default(0)->index('contract_id');
			$table->integer('buyer_id')->unsigned()->default(0)->index('buyer_id');
			$table->integer('contractor_id')->unsigned()->default(0)->index('contractor_id');
			$table->date('hourly_from')->nullable()->comment('Only for Hourly Job');
			$table->date('hourly_to')->nullable()->comment('Only for Hourly Job');
			$table->smallInteger('hourly_mins')->unsigned()->default(0)->comment('Weekly minutes for hourly job');
			$table->decimal('amount', 10)->default(0.00);
			$table->boolean('status')->default(0)->comment('0: pending, 1: available');
			$table->integer('transaction_id')->default(0);
			$table->timestamps();
			$table->softDeletes();
			$table->unique(['contract_id','buyer_id','contractor_id','hourly_from','hourly_to'], 'contract_id_2');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('hourly_reviews');
	}

}
