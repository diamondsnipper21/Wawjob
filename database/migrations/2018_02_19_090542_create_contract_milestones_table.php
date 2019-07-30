<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContractMilestonesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contract_milestones', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('contract_id')->index('contract_id')->comment('Contract id');
			$table->string('name', 500);
			$table->date('start_time');
			$table->date('end_time');
			$table->float('price', 10, 0)->default(0);
			$table->boolean('fund_status')->default(0);
			$table->boolean('payment_requested')->default(0);
			$table->boolean('performed_by')->default(0)->comment('0: Buyer, 1: Super admin');
			$table->integer('transaction_id')->default(0)->index('fund_id')->comment('Id for buyer transaction');
			$table->integer('contractor_transaction_id')->default(0)->comment('Id for freelancer transaction');
			$table->boolean('changed_status')->default(0)->comment('0:No changed, 1: Added, 2: Removed, 3: Modified');
			$table->string('new_name', 500)->nullable();
			$table->date('new_start_time')->nullable();
			$table->date('new_end_time')->nullable();
			$table->float('new_price', 10, 0)->default(0);
			$table->text('message', 65535)->nullable();
			$table->dateTime('requested_at')->nullable();
			$table->dateTime('funded_at')->nullable();
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
		Schema::dropIfExists('contract_milestones');
	}

}
