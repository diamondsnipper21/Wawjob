<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserContactsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_contacts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->default(0)->index();
			$table->string('first_name', 48);
			$table->string('last_name', 48);
			$table->boolean('gender')->default(0);
			$table->date('birthday')->nullable();
			$table->string('country_code', 2)->nullable()->index();
			$table->string('state', 48)->nullable();
			$table->string('city', 48)->nullable();
			$table->string('address', 128)->nullable();
			$table->string('address2', 128)->nullable();
			$table->string('zipcode', 16)->nullable();
			$table->string('phone', 24)->nullable();
			$table->string('fax', 24)->nullable();
			$table->integer('timezone_id')->unsigned()->default(0)->index();
			$table->string('website', 128)->nullable();
			$table->string('skype', 128)->nullable();
			$table->string('yahoo', 128)->nullable();
			$table->string('qq', 128)->nullable();
			$table->string('invoice_address', 128)->nullable();
			$table->string('invoice_city', 50)->nullable();
			$table->string('invoice_state', 50)->nullable();
			$table->string('invoice_country_code', 2)->nullable();
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
		Schema::dropIfExists('user_contacts');
	}

}
