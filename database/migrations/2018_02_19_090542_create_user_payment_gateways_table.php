<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserPaymentGatewaysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_payment_gateways', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->bigInteger('user_id')->default(0)->index('user_id');
			$table->boolean('gateway')->default(1)->index('gateway')->comment('1: Paypal, 2: Scrill, 3: Zhifubao, 4: Wire transfer');
			$table->text('data', 65535)->nullable()->comment('Gateway account like Paypal email address');
			$table->text('params', 65535)->nullable();
			$table->decimal('amount', 20)->default(0.00)->comment('Total amount holding in this account');
			$table->boolean('is_primary')->default(0);
			$table->boolean('is_pending')->default(0)->comment('0: Available, 1: In review');
			$table->boolean('status')->default(0)->comment('0: Inactive, 1: Active');
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
		Schema::dropIfExists('user_payment_gateways');
	}

}
