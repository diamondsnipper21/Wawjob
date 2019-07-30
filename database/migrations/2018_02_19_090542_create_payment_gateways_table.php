<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentGatewaysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_gateways', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name', 50)->default('');
			$table->boolean('type')->default(0);
			$table->boolean('is_bank')->default(0);
			$table->string('logo', 50)->default('');
			$table->string('fields', 250)->default('');
			$table->boolean('sort')->default(0);
			$table->boolean('is_active')->default(0);
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
		Schema::dropIfExists('payment_gateways');
	}

}
