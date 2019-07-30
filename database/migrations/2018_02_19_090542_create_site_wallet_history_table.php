<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSiteWalletHistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_wallet_history', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->boolean('type')->default(1)->index('type')->comment('1: Holding, 2: Earning');
			$table->date('date')->index('date');
			$table->integer('transaction_id')->default(0);
			$table->decimal('balance', 20)->default(0.00);
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
		Schema::dropIfExists('site_wallet_history');
	}

}
