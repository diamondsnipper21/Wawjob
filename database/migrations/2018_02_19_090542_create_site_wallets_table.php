<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSiteWalletsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_wallets', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->boolean('type')->default(1)->unique('type')->comment('1: Holding, 2: Earning');
			$table->decimal('amount', 20)->unsigned()->default(0.00);
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
		Schema::dropIfExists('site_wallets');
	}

}
