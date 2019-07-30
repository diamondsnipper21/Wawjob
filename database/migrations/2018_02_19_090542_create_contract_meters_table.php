<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContractMetersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contract_meters', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('contract_id')->unsigned()->default(0)->index('contract_id')->comment('Contract ID');
			$table->smallInteger('last_mins')->default(0)->comment('Minutes last week');
			$table->decimal('last_amount', 10)->default(0.00)->comment('Amount of last week');
			$table->smallInteger('this_mins')->unsigned()->default(0)->comment('Minutes this week
');
			$table->decimal('this_amount', 10)->unsigned()->default(0.00)->comment('Amount of this week
 (buyer)');
			$table->integer('total_mins')->unsigned()->default(0)->comment('Total minutes by now
');
			$table->decimal('total_amount', 10)->unsigned()->default(0.00)->comment('Total amounts by now
 (buyer)');
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
		Schema::dropIfExists('contract_meters');
	}

}
