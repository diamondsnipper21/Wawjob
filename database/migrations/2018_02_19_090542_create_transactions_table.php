<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transactions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean('type')->default(0)->comment('0: fixed, 1: hourly, 2: bonus, 3: charge, 4: withdrawal, 5: refund');
			$table->boolean('for')->default(0)->comment('1: From buyer, 2: To freelancer, 3: Fee');
			$table->integer('user_id')->unsigned();
			$table->integer('contract_id')->unsigned()->default(0);
			$table->date('hourly_from')->nullable()->comment('Only for Hourly Job');
			$table->date('hourly_to')->nullable()->comment('Only for Hourly Job');
			$table->smallInteger('hourly_mins')->unsigned()->default(0)->comment('Weekly minutes for hourly job');
			$table->text('note', 65535)->nullable();
			$table->decimal('amount', 20)->default(0.00);
			$table->boolean('status')->default(0)->comment('0: pending, 1: available');
			$table->dateTime('done_at')->nullable();
			$table->integer('ref_id')->unsigned()->default(0)->comment('transactions.id');
			$table->integer('ref_user_id')->default(0)->comment('Reference user id');
			$table->integer('milestone_id')->default(0)->comment('Milestone id');
			$table->integer('old_id')->default(0)->comment('Original id when cancelled transaction');
			$table->string('order_id')->nullable();
			$table->integer('user_payment_gateway_id')->default(0)->comment('user_payment_gateways.id');
			$table->boolean('checked_affiliate')->default(0);
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
		Schema::dropIfExists('transactions');
	}

}
