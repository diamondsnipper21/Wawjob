<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCountriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('countries', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('charcode', 2)->unique();
			$table->string('charcode3', 3);
			$table->string('numcode', 3);
			$table->string('name', 64)->unique();
			$table->string('country_code', 10);
			$table->softDeletes();
			$table->string('region', 50);
			$table->index(['charcode','name']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('countries');
	}

}
